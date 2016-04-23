
# 1. prepare detex, convert 70% image file

# download/clone detex
git clone https://github.com/hglm/detex.git

cd detex

# compile detex, but may lose png.h on mac
make detex-convert

# if png.h not find... copy png.h to here
make clean
locate png.h | tail -n 1 | xargs -I {} cp {} .
locate pnglibconf.h | tail -n 1 | xargs -I {} cp {} .
locate pngconf.h | tail -n 1 | xargs -I {} cp {} .
ls png.h pnglibconf.h pngconf.h

make detex-convert
ls detex-convert

cd ..

# 2. prepare decompress-pvrtc, convert PVRTC format image

# download/clone decompress-pvrtc
git clone https://github.com/tlozoot/decompress-pvrtc.git

# change decompress.c for convince interface. 
cp decompress_bmp.c decompress-pvrtc/decompress.c

# Important! decompress-pvrtc is converting pvrtc rawdata to bmp format
# if you want to convert to png use another interface, but you need install opencv first
# cp decompress_png.c decompress-pvrtc/decompress.c

# compile decompress-pvrtc
cd decompress-pvrtc
make
ls decompress

cd ..

# now use run.sh to pull unity files in tos app and convert them to png/bmp images
echo "you can run 'sh run.sh' for next step"
