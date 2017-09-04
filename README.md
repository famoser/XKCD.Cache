# XKCD.Cache
This is a cache of the comics at XKCD. It allows to download a zip with all published images in a single request. 

this webapplication is used in http://github.com/famoser/XKCD

[![Travis Build Status](https://travis-ci.org/famoser/XKCD.Cache.svg?branch=master)](https://travis-ci.org/famoser/XKCD.Cache)
[![Code Climate](https://codeclimate.com/github/famoser/XKCD.Cache/badges/gpa.svg)](https://codeclimate.com/github/famoser/XKCD.Cache)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0049282fe1b3437ba8321ec244a3ea93)](https://www.codacy.com/app/famoser/XKCD.Cache)
[![Scrutinizer](https://scrutinizer-ci.com/g/famoser/XKCD.Cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/famoser/XKCD.Cache)
[![Test Coverage](https://codeclimate.com/github/famoser/XKCD.Cache/badges/coverage.svg)](https://codeclimate.com/github/famoser/XKCD.Cache/coverage)

## Endnodes

### /
displays a html explanation page

### /download/zip
downloads the zip with the image files contained in the cache

### /download/json
downloads a json with all json data contained in the cache

### /refresh
downloads all images & json missing from the cache. the response is of the following form:

```
{
    "successful": true,
    "error_message": null,
    "missing_images": [],
    "missing_json": []
}
```
	
`successful` can be `true` or `false`. This should be true.  
`missing_images` contains an array of all images it failed to download. This array might not be empty.  
`missing_json` contains an array of all json it failed to download. This array should be empty.  
`error_message` is a string and contains the error if `successful` is set to true.  


### /status
checks if the cache is hot (all images all downloaded). the response is of the following form:  

```
{
    "successful": true,
    "error_message": null,
    "hot": true,
    "latest_image_published": 400,
    "latest_image_cached": 400,
    "api_version": 1
}
```
	
`successful` can be `true` or `false`. This should be true.  
`error_message` is a string and contains the error if `successful` is set to true.  
`hot` can be `true` or `false`. If set to `true` the cache is actual. If set to `false`, you may call `/refresh` to refresh the cache.  
`latest_image_published` contains the number of the last published image on XKCD.
`latest_image_cached` contains the number of the last cached image in the cache. this number is smaller / equal the number in `latest_image_published` and ideally the same.
`api_version` contains the version of the deployed API.