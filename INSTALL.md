# Install

Any2Web is a simple service intented to be run from Apache but can be adjusted to work under Lighttpd or Nginx.
We assume your environment already has PHP and an existing web-server that you can configure.

 * Clone this repo to some place good
 * Install Dependencies
 * Run Composer
 * Configure Apache

## Common

	cd /opt
	git clone https://github.com/edoceo/any2web.git
	mkdir var
	chown www-data:www-data var

## Ubuntu Dependencies

	apt-get install ghostscript imagemagick libreoffice pdftk zip

### International Characters

On Ubuntu each of these has to be installed, this set covers the more "common" languages.
For the full set please see `apt-cache search libreoffice-l10n`

	apt-get install \
		libreoffice-l10n-ar \
		libreoffice-l10n-de \
		libreoffice-l10n-en-gb \
		libreoffice-l10n-es \
		libreoffice-l10n-fr \
		libreoffice-l10n-he \
		libreoffice-l10n-hi \
		libreoffice-l10n-id \
		libreoffice-l10n-ja \
		libreoffice-l10n-ko \
		libreoffice-l10n-nl \
		libreoffice-l10n-pl \
		libreoffice-l10n-pt \
		libreoffice-l10n-pt-br \
		libreoffice-l10n-ru \
		libreoffice-l10n-th \
		libreoffice-l10n-tr \
		libreoffice-l10n-vi \
		libreoffice-l10n-zh-cn \
		libreoffice-l10n-zh-tw

### Fonts!

	apt-get install \
		fonts-sipa-arundina \
		fonts-meera-taml \
		fonts-lohit-guru \
		fonts-lohit-gujr \
		fonts-lohit-beng-bengali \
		fonts-lohit-taml \
		fonts-dejavu \
		ttf-indic-fonts \
		xfonts-thai

#### Hindi specifically see:

 * http://askubuntu.com/questions/447050/adding-hindi-fonts-to-ubuntu-font-family
 * http://www.cghs.nic.in/hindiFont.jsp
 * https://lists.ubuntu.com/archives/ubuntu-in/2011-June/010573.html

## Gentoo Dependencies

	emerge -av \
		app-arch/zip \
		app-office/libreoffice-bin \
		app-office/libreoffice-l10n \
		app-text/ghostscript-gpl \
		app-text/pdftk \
		media-gfx/imagemagick

### Fonts!

	emerge -av \
		media-fonts/liberation-fonts \
		media-fonts/urw-fonts
