# Using custom fonts
-------------------------------

It is possible to use custom fonts, however, it is not easy. Most of the steps described here are copied from the pdf (a copy is available in this dir, [source](http://c.caignaert.free.fr/Install-ttf-Font.pdf)). I can recommend to read the complete PDF, but the simple steps are given below.

For these steps I will use Gill Sans MT.

### Use the font
-------------------------------

To use the font in the document, include the correct packages and map (which will be created later). You will need to specify the font encoding as T1.

```
\usepackage[T1]{fontenc}
\usepackage{GillSansMT}
\pdfmapfile{+GillSansMT.map}
```

Do not forget to add the dir with all needed files as dependency for the latex generator. 

### Build the necessary files
-------------------------------

**fd file**
Determines the internal name of the font, in our case `T1gsmt.fd`. 

- *Encoding*: T1 --[why?]()--
- *Family*: gsmt
- *Weight*: medium-m or bold-b
- *Shape*: upright-n or italic-it

Content: 

```
\ProvidesFile{T1gsmt.fd}
  [2014/05/12 Font definitions for T1/gsmt.]

\DeclareFontFamily{T1}{gsmt}{}

\DeclareFontShape{T1}{gsmt}{m}{n} {<-> gsmtmn8t } {}
\DeclareFontShape{T1}{gsmt}{b}{n} {<-> gsmtbn8t } {}
\DeclareFontShape{T1}{gsmt}{m}{it}{<-> gsmtmit8t } {}
\DeclareFontShape{T1}{gsmt}{b}{it}{<-> gsmtbit8t } {}
\DeclareFontShape{T1}{gsmt}{m}{sc}{<-> ssub * gsmt/b/n} {}
\DeclareFontShape{T1}{gsmt}{m}{sl}{<-> ssub * gsmt/m/it}{}
\DeclareFontShape{T1}{gsmt}{sb}{n} {<-> ssub * gsmt/b/n} {}
\DeclareFontShape{T1}{gsmt}{sb}{sc}{<-> ssub * gsmt/b/n} {}
\DeclareFontShape{T1}{gsmt}{sb}{sl}{<-> ssub * gsmt/b/it}{}
\DeclareFontShape{T1}{gsmt}{sb}{it}{<-> ssub * gsmt/b/it}{}
\DeclareFontShape{T1}{gsmt}{b}{sc} {<-> ssub * gsmt/b/n} {}
\DeclareFontShape{T1}{gsmt}{b}{sl} {<-> ssub * gsmt/b/it} {}
\DeclareFontShape{T1}{gsmt}{bx}{n} {<-> ssub * gsmt/b/n} {}
\DeclareFontShape{T1}{gsmt}{bx}{sc}{<-> ssub * gsmt/b/n} {}
\DeclareFontShape{T1}{gsmt}{bx}{sl}{<-> ssub * gsmt/b/it} {}
\DeclareFontShape{T1}{gsmt}{bx}{it}{<-> ssub * gsmt/b/it} {}

\endinput
```

**map file**

This file links the ttf and the internal font names: `GillSansMT.map`.

Content: 

```
gsmtmn8t GillSansMT <GillSansMT.ttf <T1-WGL4.enc
gsmtbn8t GillSansMTBold <GillSansMTb.ttf <T1-WGL4.enc
gsmtmit8t GillSansMTItalique <GillSansMTi.ttf <T1-WGL4.enc
gsmtbit8t GillSansMTBoldItalique <GillSansMTbi.ttf <T1-WGL4.enc
```

You see that the file T1-WGL4.enc is used. This file is also needed during compilation of the latex, and is added to this repository in `Resources/fonts`.

**tfm files**

Every ttf file needs to be converted to tfm: `*.tfm`.
You will need to run two (terminal) commands for every ttf file:

```
ttf2tfm GillSansMT.ttf -q -T T1-WGL4.enc -v GillSansMT.vpl GillSansMT.tfm
vptovf GillSansMT.vpl GillSansMT.vf GillSansMT.tfm
```

Do not forget to rename the resulting tfm file to `{family}{weight}{shape}8t.tfm`. Example: `gsmtmn8t.tfm`. That is the normal font with T1 (8t) encoding. 


**sty file**

The package to use the font as default: `GillSansMT.sty`.

Content: 

```
\ProvidesPackage{GillSansMT}
  [2014/05/12 LaTeX package loading Gill Sans MT TTF font]
  \renewcommand{\rmdefault}{gsmt}
\endinput
```

### Install the font correctly
---------------------------

You will (at least) need to install the tfm files to the latex directory and rebuild the database. This can be done by the following commands from the fonts dir (Debian 7): 

```
sudo mkdir /usr/share/texlive/texmf-dist/fonts/tfm/GSMT
sudo mkdir /usr/share/texlive/texmf-dist/fonts/GSMT
sudo cp *.tfm /usr/share/texlive/texmf-dist/fonts/tfm/GSMT/
sudo cp *.ttf /usr/share/texlive/texmf-dist/fonts/GSMT/
sudo texhash /usr/share/texlive/texmf-dist
```

I used the directoy GSMT to install the fonts so that I can identify them in the installation later. The choice of the directory is completely up to you.


### Problems with fonts
---------------------------

When running LaTeX on Debian, and you encounter the problem below (or similar):
```
!pdfTeX error: pdflatex (file ectt1095): Font ectt1095 at 600 not found
==> Fatal error occurred, no output PDF file produced!
```
execute the following command: `sudo apt-get install texlive-fonts-extra`

**Note**: Commit #2079747 contains an update which sets the 'HOME' environment variable. If there is no 'HOME' environment variable when fonts need to be generated at runtime, some subscripts of LaTeX might fail. This should not impact working installations.
**Note**: Commit #5143e0c changes the value of the HOME variable to /tmp
