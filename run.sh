
echo "Insure android phone is link to computer...";

mkdir tmp_unity
cd tmp_unity

adb pull /storage/emulated/legacy/Android/data/com.madhead.tos.zh/files/bundles
ls *.zip | xargs -I{} unzip {}
rm *.zip

cd ..

ls tmp_unity/* | xargs -I{} php convert2png.php {} > log 2>&1