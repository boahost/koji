#!/bin/bash

# Diretório dos ícones
ICON_DIR="public/images"
mkdir -p "$ICON_DIR"

# Cria um ícone base em SVG
cat > "$ICON_DIR/base-icon.svg" << 'EOF'
<svg width="512" height="512" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="512" height="512" fill="#0EA5E9"/>
    <text x="256" y="300" font-family="Arial" font-size="240" font-weight="bold" fill="white" text-anchor="middle">DF</text>
</svg>
EOF

# Lista de tamanhos para gerar
SIZES=(72 96 128 144 152 192 384 512)

# Gera os ícones em PNG para cada tamanho
for size in "${SIZES[@]}"; do
    convert "$ICON_DIR/base-icon.svg" -resize "${size}x${size}" "$ICON_DIR/icon-${size}x${size}.png"
done

# Remove o arquivo SVG base
rm "$ICON_DIR/base-icon.svg"
