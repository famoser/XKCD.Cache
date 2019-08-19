# XKCD.Cache
This is a cache of the comics at XKCD. It allows to download a zip with all published images in a single request. 

[![Travis Build Status](https://travis-ci.org/famoser/XKCD.Cache.svg?branch=master)](https://travis-ci.org/famoser/XKCD.Cache)
[![Code Climate](https://codeclimate.com/github/famoser/XKCD.Cache/badges/gpa.svg)](https://codeclimate.com/github/famoser/XKCD.Cache)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/0049282fe1b3437ba8321ec244a3ea93)](https://www.codacy.com/app/famoser/XKCD.Cache)
[![Scrutinizer](https://scrutinizer-ci.com/g/famoser/xkcd.cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/famoser/XKCD.Cache)
[![Test Coverage](https://codeclimate.com/github/famoser/xkcd.cache/badges/coverage.svg)](https://codeclimate.com/github/famoser/XKCD.Cache/coverage)

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
    "missing_json": [],
    "refresh_count": 0,
    "refresh_cap": 10,
    "refresh_pending": false
}
```
	
`successful` is a boolean indicating if the request was successful (`true`) or if an error occurred (`false`).  
`missing_images` contains an array of all images it failed to download. This array might not be empty.  
`missing_json` contains an array of all json it failed to download. This array should be empty.  
`error_message` is a string and contains the error if `successful` is set to true.  
`refresh_count` is an int indicating how many images were downloaded in the refresh step  
`refresh_cap` is an int indicating how many images are max downloaded in each refresh step  
`refresh_pending` is a bool which is `true` if further refresh calls are necessary to refresh cache

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
	
`successful` is a boolean indicating if the request was successful (`true`) or if an error occurred (`false`).
`error_message` is a string and contains the error if `successful` is set to true.  
`hot` is a boolean. If set to `true` the cache is fresh (no files missing). If set to `false`, you may call `/refresh` to refresh the cache.  
`latest_image_published` is an int which is the number of the last published image on XKCD.
`latest_image_cached` is an int which is the number of the last cached image in the cache. this number is smaller / equal the number in `latest_image_published` and ideally the same.
`api_version` is an int containing the version of the deployed API.