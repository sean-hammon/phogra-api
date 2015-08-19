## Phogra API

I wanted some practice building an API so I decided to work on an API for PHOtoGRAphs. I have been a hobby photographer
for a long time and have used lots of different photo gallery options over the years.
I have never been really happy with any of them so I decided to take another stab at making my own.

I've worked with a few different APIs in the last few years. I even wrote one at one point. As is usually the case,
I would write it differently if I did it today. I've spent time looking at HATEOAS: 
[Collection+JSON](http://amundsen.com/media-types/collection/examples/), [HAL](http://stateless.co/hal_specification.html),
[Siren](https://github.com/kevinswiber/siren), et al. They all feel really over engineered to me. Probably because 
they're trying to be all things to all people. The folks at [foxcart](http://www.foxycart.com)
have a good [write up](http://www.foxycart.com/blog/the-hypermedia-debate) on the travails of someone trying
to figure out which one works best for them. This API will be based on [JSON API](http://jsonapi.org), but I don't plan 
on adhering strictly to the standard. It will eventually be the API that drives my
[Phogra UI](https://github.com/sean-hammon/phogra-ui) project.

The API will have the ability auto-generate thumbnails and alternate file sizes for you. If this is a feature
you'll want to use, PHP may need more than the default memory allotment. 128M should be enough to process most
standard photo sized images (up to 12MB or so). It was not enough to process a 30MB image that I had stitched together
from several shots. After increasing PHP's memory max to 256M, I was able to process the 30MB image. Your mileage may
vary.