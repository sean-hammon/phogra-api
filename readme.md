## Phogra API

I wanted some practice building an API so I decided to work on an API for PHOtoGRAphs. I have been a hobby photographer
for a long time and have used lots of different photo gallery options over the years.
I have never been really happy with any of them so I decided to take another stab at making my own.

There have been several false starts so the code is kind of a mess right now. It will eventually be the API that drives my
[https://github.com/sean-hammon/phogra-ui](Phogra UI) project.

The API will have the ability auto-generate thumbnails and alternate file sizes for you. If this is a feature
you'll want to use, PHP may need more than the default memory allotment. 128M should be enough to process most
standard photo sized images (up to 12MB or so). It was not enough to process a 30MB image. 
After increasing PHP's memory max to 256M, I was able to process the 30MB image.