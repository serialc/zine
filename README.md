zine
====
A simple PHP collaborative zine layout and page submission web based application

Originally developed to create a Zine called 'Hang' in Luxembourg.

After installation a little bit of customization is necessary.

To get it working
====
1. Change permissions: chmod 666 /zine/ed/issues.txt
2. Update .htaccess path: edit /zine/ed/.htaccess to point to /zine/ed/.editorsp
3. Should work now.

Login
====
All the magic is in /zine/ed/
username: editor
password: editor (obviously need to change this before putting it live)

Needs to be completed
====
Export to layout
- this should create a pdf with the images tiled correctly (flipped etc...) to print double sided
