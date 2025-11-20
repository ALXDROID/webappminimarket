#!/usr/bin/env bash
set -euo pipefail

# Script seguro para convertir el archivo 'img' en carpeta y mover .jpg allí.
# USO: desde la raíz del repo: bash scripts/convert-img-to-dir.sh

BRANCH="fix/convert-img-to-dir-$(date +%s)"
echo "Creando rama: $BRANCH"
git checkout -b "$BRANCH"

# Comprobar que el working tree está limpio
if ! git diff --quiet || ! git diff --cached --quiet; then
  echo "ERROR: tienes cambios sin commitear. Haz commit o stash antes de continuar."
  exit 1
fi

# Si 'img' está trackeado como archivo, hacer backup (mv) y commitear el cambio
if git ls-files --error-unmatch -- "img" >/dev/null 2>&1; then
  if [ -f "img" ]; then
    echo "El path 'img' está trackeado como archivo. Lo voy a mover a img.bak y commitear el cambio."
    git mv "img" "img.bak"
    git commit -m "Backup: mover archivo 'img' a img.bak para convertirlo en directorio"
  else
    echo "'img' está trackeado pero no es un archivo regular. Revisa manualmente."
  fi
else
  echo "'img' no está trackeado como archivo; continuando."
fi

# Crear directorio img/
mkdir -p img

# Buscar y mover archivos .jpg/.jpeg (excluyendo .git y la nueva carpeta img)
echo "Buscando archivos .jpg/.jpeg y moviéndolos a img/ (excluyendo .git y ./img/)..."
find . -type f \( -iname '*.jpg' -o -iname '*.jpeg' \) \
  -not -path "./.git/*" -not -path "./img/*" -not -path "./node_modules/*" -not -path "./dist/*" \
  -print0 | while IFS= read -r -d '' file; do
    base="$(basename "$file")"
    echo "  -> moviendo '$file' a 'img/$base'"
    dest="img/$base"
    if [ -e "$dest" ]; then
      ts="$(date +%s)"
      dest="img/${base%.*}-$ts.${base##*.}"
      echo "     archivo destino existe, renombrando a $dest"
    fi
    mkdir -p "$(dirname "$dest")"
    # Preferimos git mv cuando el archivo está trackeado; si falla, usar mv
    if git ls-files --error-unmatch -- "$file" >/dev/null 2>&1; then
      git mv -f -- "$file" "$dest"
    else
      mv -- "$file" "$dest"
      git add "$dest" >/dev/null 2>&1 || true
    fi
done

# Añadir cambios y commitear el movimiento (si hay algo para commitear)
if ! git diff --quiet || ! git diff --cached --quiet; then
  git add -A
  git commit -m "Convertir 'img' a directorio y mover archivos .jpg/.jpeg dentro"
  echo "Commit creado con los cambios de movimiento."
else
  echo "No se han detectado archivos .jpg/.jpeg para mover."
fi

echo ""
echo "Siguiente paso recomendado: buscar en el código referencias a .jpg y actualizarlas para apuntar a 'img/<nombre>.jpg'."
echo "Lista de archivos que referencian imágenes:"
echo "  git grep -n --extended-regexp '\\\\.(jpe?g)' || true"
echo ""
echo "Si quieres un reemplazo automático (usa con cuidado), un ejemplo para actualizar rutas relativas sería:"
echo "  git grep -l --extended-regexp '\\\\.(jpe?g)' | xargs -I{} sed -i.bak -E 's#(src=[\"\\'']?)[^\"\\' ]*/([^/ ]+\\.(jpe?g))#\\1img/\\2#g' {}"
echo "Revisa los .bak y los cambios antes de commitear reemplazos automáticos."
echo ""
echo "Cuando estés listo, sube la rama:"
echo "  git push -u origin $BRANCH"