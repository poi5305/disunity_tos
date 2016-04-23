#include <stdlib.h>
#include <stdio.h>
#import <stdint.h>
#include <limits.h>
#include <math.h>
#include <string.h>
#include <time.h>
#include "pvrtdecompress.h"
#include <opencv2/opencv.hpp>

const uint16_t bmpfile_magic = 0x4d42;

struct AMTC_BLOCK_STRUCT;

size_t get_size(FILE *fp);
size_t bmp_init(FILE *fp, int w, int h);
void write_noise(FILE *fp, int dim);
void hex_dump(void *buffer, size_t size);
void pvrtdecompress(AMTC_BLOCK_STRUCT *input_buf, const int is_2bpp,
                    const int x_dim, const int y_dim,
                    unsigned char *result_buf);

int main(int argc, char const *argv[])
{
	if (argc < 5)
    {
	    printf("usage: ./decompress width height input_image output_image\n");
	    exit(0);
    }
	
    int width = atoi(argv[1]);
    int height = atoi(argv[2]);
    int do_2bpp = 0;//(argv[1] && atoi(argv[1]) == 2) ? 1 : 0;
    const char *infile_name = argv[3]; //do_2bpp ? "img/firefox-2bpp.pvr" : "img/firefox-4bpp.pvr";
    const char *outfile_name = argv[4]; //do_2bpp ? "img/firefox-2bpp.bmp" : "img/firefox-4bpp.bmp";
    
    // Initialize infile
    FILE *infile = fopen(infile_name, "rb");
    size_t insize = get_size(infile);
    void *inbuffer = malloc(insize * sizeof(char));
    
    // Initialize outfile
    size_t outsize = width * height * 4 * sizeof(char);
    void *outbuffer = malloc(outsize);
                
    fread(inbuffer, 1, insize, infile); 

    pvrtdecompress((AMTC_BLOCK_STRUCT*) inbuffer, do_2bpp, width, height, (unsigned char *) outbuffer);

	cv::Mat image = cv::Mat(height, width, CV_8UC4, (void *)outbuffer);
	cv::imwrite(outfile_name, image);
        
    fclose(infile);
    
    free(inbuffer);
    free(outbuffer);
    
	return 0;
}

size_t get_size(FILE* fp)
{
    fseek(fp, 0, SEEK_END);
    size_t size = ftell(fp);
    fseek(fp, 0, SEEK_SET);
    return size;
}

size_t bmp_init(FILE *fp, int w, int h)
{
    // Magic
    fwrite(&bmpfile_magic, 2, 1, fp);
    
    // BMP File header
    BITMAPFILEHEADER bf;
    bf.creator1 = 0;
    bf.creator2 = 0;
    bf.bmp_offset = 2 + sizeof(BITMAPFILEHEADER) + sizeof(BITMAPINFOHEADER);
    
    // BMP Info header
    BITMAPINFOHEADER bi;
    bi.header_sz = 40;
    bi.width = w; 
    bi.height = h;
    bi.nplanes = 1;
    bi.bitspp = 32;
    bi.compress_type = 0;
    bi.bmp_bytesz = bi.width * bi.height * sizeof(RGBQUAD);
    bi.hres = 2835;
    bi.vres = 2835;
    bi.ncolors = 0;
    bi.nimpcolors = 0;    
    
    bf.filesz = bi.bmp_bytesz + bf.bmp_offset; 
    
    fwrite(&bf, sizeof(BITMAPFILEHEADER), 1, fp);
    fwrite(&bi, sizeof(BITMAPINFOHEADER), 1, fp);
    
    return (size_t) bi.bmp_bytesz;
}

void write_noise(FILE *fp, int dim)
{
    srand(time(NULL));
    for (int i = 0; i < dim; i++) {
        for (int j = 0; j < dim; j++) {
            char r = rand() % 255;
            char g = rand() % 255;
            char b = rand() % 255;
            char a = rand() % 255;
            RGBQUAD pixel = {b, g, r, a};
            fwrite(&pixel, sizeof(RGBQUAD), 1, fp);
        }
    }    
}

void hex_dump(void *buffer, size_t size)
{
    for (int i = 0; i < (int) size; i++) {
        printf("%x ", *((char *) buffer + i));
    }
    printf("\n");   
}
