LightFM
=======
*Very early development stage, not ready for production work! :)*

At v0.4 beta, the server side should be mostly done, so it *should* work, although the user interface (html,css, js) is still not complete and not tested in anything else than webkit (Chromium and so).

A light file manager and browser written in PHP with Nette framework. 
Directories can be protected by password from public access and directory owners can upload, move or delete their files. Does not needs any database.

Licensed under New BSD license and also GNU GPLv2 - use your preffered one (see LICENSE.txt file)

Installation
------------
1) Place the files
2) Give the webserver a full access (rwx) to directories LightFM/temp and LightFM/www (recursively)
3) (Optional) Edit basepath in LightFM/www/.htaccess
4) Change default configuration at the begining of LightFM/app/config/config.neon
5) Change the usernames and password (or create new) in LightFM/www/.filenamanger.ini
6) Use it :-)

.filemanager.ini
-----------------------
The settings are applied recursively - all subdirs share all settings with the parent, if they are not changed and configured to something else.
Most of the configuration can be changed also through the web interface.

    ; this is comment

    ; This is access password - without it the folder will be public. Default: empty
    accessPassword="some secret"

    ; Users are needed for file management. Default: set via .neon
    ; New users can be set only in the root directory, in other files it
    ; will be ignored.

    users[username]=password                ; this is an user with a password

    ; Usernames of users who ownsthis directory (can manipulate with the files 
    ; and change its settings).
    owners[]=username

    ; If is set to true, visitors can download all or selected files as a zip. Default: false
    allowZip=true

    ; time of last change - for testing if someone changed it alredy
    lastChanged=123456789

Custom views
------------
If you want you can create a custom view for the directories (like the list "all files" and the gallery "images only"), or for files itself. Each view is representing one presenter (MVC architecture).

To do this, your view must be a presenter implementing the abstract class ADirectoryPresenter. For more details, look at the GalleryPresenter and ListPresenter.

Also you can create your view for files - in that case your new presenter have to extends the FilePresenter and you have to define it in some filetype class to be used.
For creating a custom filetype, looks into model/Files. It is neccessary to extend the File class and possibly you can implements the IText/IImage interface if your file can be considered as a text file, or an image file.
Again, for further details look at existing TextFile/ImageFile.

The ImageFile is used automaticaly on files which php recognize as an image, while the TextFile is set to defined mimetypes (private static $mimeHighlight).


Note: 
-----
Highlight library is only under GPLv2 so it shouldn't be included under the BSD 
license, but during the initial heavy development I'm keeping it included, once 
things settle little, I will change it.

Limits for creating Zip files (number of files and so...) can be changed in app/model/IArchiver.

Changelog:
---------
v0.5 beta 31. Mar. 2013
- Ajax again enabled
- Added basic IE support to make the system usable (not working mediaquery and so but I'm too lazy to work on it now)
- LESS is now compiled to CSS
- Various smaller enahncements
- Various bugs correct

v0.4 beta
- Changed temp dirs
- Refactored javascript
- Various smaller enahncements
- Various bugs correct

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