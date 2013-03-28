LightFM
=======
*Very early development stage, not ready for production work! :)*

A light file manager and browser written in PHP with Nette framework. 
Directories can be protected by password from public access and directory owners can upload, move or delete their files. Does not needs any database.

Licensed under New BSD license and also GNU GPLv2 - use your preffered one (see LICENSE.txt file)

Note: 
-----
Highlight library is only under GPLv2 so it shouldn't be included under the BSD 
license, but during the initial heavy development I'm keeping it included, once 
things settle little, I will change it.

Limits for creating Zip files (number of files and so...) can be changed in app/model/IArchiver.

Changelog:
---------
v0.3 alpha - 28. Mar. 2013
- Added file operations (delete, move, ...)
- Ajax is temporary disabled for development
- Various smaller enahncements
- Various bugs correct


v0.2 alpha
- Added ajax
- Various smaller enahncements
- Various bugs correct

v0.1 alpha - 24. Mar. 2013
- Basic functions for browsing are working, but still many missing things