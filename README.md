**Requirements**
- docker (https://docs.docker.com/engine/install/)
- docker-compose (https://docs.docker.com/compose/install/linux/)

**How to install**

1. Clone repository:
   - `git clone https://github.com/paprykasz/imager`
2. Change directory to cloned repository `imager`
3. Generate certificate by running:
   - `.config/docker/apache2/certs/generate`
4. Run docker services 
   - `docker-compose up -d`
5. Install php dependencies
   - `docker exec -it phpfpm composer install`
6. Open url in browser to see example images `https://localhost`

**How to use**

- Original image storage folder is located at `storage/img/` you need to save original image there.
- After that there are two modifiers available:
  - crop - you need to visit https://localhost/{originalImageNameWithExtension}/crop/{height}x{width}
  - resize - you need to visit https://localhost/{originalImageNameWithExtension}/resize/{height}x{width}
- At the end you will get redirected to new generated image url, created image will be placed in ``public/`` directory

**How to test**

 `docker exec -it phpfpm vendor/bin/phpunit`

**Heads up!**

Ensure that port 443 on the Docker host is not already in use and that your host's firewall allows inbound access on that port.

**Image copyrights:**

Photo by <a href="https://unsplash.com/@emben?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Johann Siemens</a> on <a href="https://unsplash.com/s/photos/tree?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>
Photo by <a href="https://unsplash.com/es/@snowjam?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">John McArthur</a> on <a href="https://unsplash.com/@snowjam?utm_source=unsplash&utm_medium=referral&utm_content=creditCopyText">Unsplash</a>  