**How to install**

- git clone https://github.com/paprykasz/imager
- change directory to `imager`
- run docker services `docker-compose up -d`
- run `docker exec -it phpfpm composer install` to install php dependencies
- open `https://localhost` in browser

**How to use**

- Original image storage folder is located at `storage/img/` you need to save original image there.
- After that there are two modifiers available:
  - crop - you need to visit https://localhost/{originalImageNameWithExtension}/crop/{height}x{width}
  - resize - you need to visit https://localhost/{originalImageNameWithExtension}/resize/{height}x{width}
- At the end you will get redirected to new generated image url, created image will be placed in ``public/`` directory

**How to test**
- docker exec -it phpfpm vendor/bin/phpunit

**Heads up!**

Ensure that port 443 on the Docker host is not already in use and that your host's firewall allows inbound access on that port.

**Image copyrights:**

Photo by <a href="https://unsplash.com/@emben?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Johann Siemens</a> on <a href="https://unsplash.com/s/photos/tree?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>
  