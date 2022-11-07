---
type:               "post"
title:              "D'OSX à Linux (en milieu professionnel) - Partie 1 - Le quotidien"
date:               "2022-10-25"
lastModified:       "2022-11-18"
tableOfContent:     true
description:        "Passer d'OSX à Linux en milieu professionnel, le quotidien, équivalence d'applications, fonctionnement, astuces."

thumbnail:          "content/images/blog/thumbnails/inside-linux.jpg"
tags:               ["linux", "osx"]
categories:         ["linux"]
authors:            ["gfaivre"]
---

Parce que le choix (ou la nécessité) de changer de système d'exploitation remet en cause notre utilisation quotidienne et nos réflèxes applicatifs, il me parait louable de partager **MA** façon d'utiliser Linux après de nombreuses années avec OSX et quelles solutions j'ai « trouvé » pour reprendre mes marques notamment en terme d'équivalence d'applications et d'utilisation au quotidien.

Billet à destination de celles et ceux qui hésitent encore à franchir le pas de peur de se retrouver perdu·e.

## Introduction

En préambule et pour mieux cerner certaines contraintes que j'ai pu avoir ou choix que j'ai pu faire:

- J'utilise Linux à titre `pro et perso` depuis plus de 20 ans (J'ai commencé avec Debian/Potato);
- `Je ne prêche pour aucune distribution en particulier`, sauf côté serveur, mais ça n'est pas le sujet;
<br/>Il existe aujourd'hui suffisamment de distributions « matures » pour que chacun y trouve son bonheur, il n'y a pas de « meilleure » distro il y a **celle qui vous convient**. En ce qui me concerne j'ai eu de la **Debian** (beaucoup), de la **Fedora** et de l'**Ubuntu** (un peu pour les deux) et ça fait maintenant 3 ans que j'utilise **Manjaro** après un rapide passage sur **Antergos**;
- J'ai passé pas loin de 10 ans avec OSX coté pro avec ses bons et ses mauvais côtés et même si je ne m'y retrouve plus aujourd'hui, c'est toujours à mon sens un bon OS;
- J'utilise `Gnome` comme environnement graphique;
- Je dispose de plusieurs adresses de messagerie dont pas mal sont gérées par ProtonMail;
- Mon quotidien est celui d'un SysAdmin/Ops/SRE (Je ne sais plus où on en est dans les appellations) bref tout ce qui va toucher à l'administration système et à l'outillage autour.

Je séparerai cet article en plusieurs parties en fonction des différentes utilisations:
- Le quotidien (qui pourra intéresser tout un chacun);
- Le spécifique (qui relève de mon utilisation);
- Le pro (très focalisé sur le métier).
- Le cosmétique

Sans plus attendre un premier chapitre qui va se concentrer sur l'utilisation que chacun d'entre nous peut avoir d'une machine.

## Les clients de courriels

L'une des premières utilisation, les courriels !<br/>
J'utilise uniquement les clients de courriel et quasiment jamais un navigateur pour consulter ma messagerie. Avec OSX j'utilisais l'excellent [`Postbox`](https://www.postbox-inc.com/).

__Du côté de linux j'ai retenu deux candidats:__

- L'incontournable Thunderbird qui reste, fonctionnellement et de très loin le plus complet. Son ergonomie est toutefois très datée et on ne va pas se le cacher, l'organisation de ses menus est tout de même un sacré bordel ! À son crédit c'est un des rares à supporter GPG nativement.
- Celui que j'utilise depuis quelques temps, [`Mailspring`](https://getmailspring.com/) qui dispose d'une interface beaucoup plus simple et accessible et fonctionne très bien avec le bridge de chez [`ProtonMail`](https://proton.me/fr/mail).

<figure>
    <img src="/content/images/blog/2022/osx-to-linux/mailspring-screenshot-01.jpg" alt="L'interface de Mailspring">
    <figcaption>
      <span class="figure__legend">L'interface de Mailspring</span>
    </figcaption>
</figure>

À noter que j'attends la refonte annoncée de Thunderbird avec impatience tant le fonctionnel de Mailspring reste « relativement » limité.<br/>
(**Mise à jour du 18 Novembre 2022**: Quelques écrans de la nouvelle interface ont fait leur apparition, ils semblent augurer d'un gros et bon travail d'UI.)

## Les messageries (perso)

Au niveau des messageries j'utilise principalement `Signal` et `Whatsapp` (pour les proches), les deux disposent d'intégration des clients comme je l'avais sous OSX rien à signaler à ce niveau on est ISO !

## Les navigateurs

Probablement la brique la plus standardisée à l'heure actuelle, on retrouve exactement la même chose côté Linux c'est donc plus l'affinité de chacun qui va jouer.
 De mon côté j'en utilise deux principalement [`Brave`](https://brave.com/fr/) (sur une base chromium donc) et [`Librewolf`](https://librewolf.net/) (sur une base Firefox).

 À ces derniers vient se greffer [`TorBrowser`](https://www.torproject.org/fr/download/).

## La bureautique

### Documents et tableurs
Ah ! La bureautique…

Point de friction lié principalement à la comptabilité avec les formats propriétaires, je n'ai plus rencontré de « vrai » problème depuis un moment.
J'utilise `OnlyOffice Desktop` pour l'édition de document de type Word et Excel et ça juste marche !

Point plus embêtant si vous avez des imprimantes réseau un peu anciennes il est probable qu'il vous soit compliqué d'imprimer sans faire un effort de configuration (je ne l'ai pas fait, je n'imprime quasiment jamais).

### PDF
Du côté des PDFs, le visionneur de documents de Gnome fait très bien le boulot pour de la lecture.<br/>
Au niveau de l'édition c'est un domaine ou il faut encore bricoler par rapport à OSX qui est très bon nativement pour manipuler les PDFs.
J'utilise donc [Xournal++](https://framalibre.org/content/xournal) dès lors que je dois y insérer des images ou autres éléments mais il s'agit d'un projet qui semble être à l'abandon et pour lequel je n'ai pas encore cherché/trouvé d'alternative viable.

<figure>
    <img src="/content/images/blog/2022/osx-to-linux/visionneuse-pdf.jpg" alt="La visionneuse de Gnome">
    <figcaption>
      <span class="figure__legend">La visionneuse de Gnome</span>
    </figcaption>
</figure>

## Les agendas

Là encore l'offre est plétorique, après avoir pas mal utilisé [`Lightning`](https://www.thunderbird.net/en-US/calendar/) (Thunderbird) je suis revenu sur [`Gnome Calendar`](https://wiki.gnome.org/Apps/Calendar) qui fait le boulot.

**ATTENTION** toutefois il n'intègre pas la mécanique d'invitations, si vous planifiez beaucoup de réunions il vaut sans doute mieux rester sur Lightning.

Pour ceux qui disposent d'agendas iCloud (j'en fais partie) leur ajout à Lightning passe par l'utilisation de l'extension [TBSync](https://addons.thunderbird.net/en-US/thunderbird/addon/tbsync/) quant à Gnome Calendar il faut passer par une gymnastique assez lourde que je décris [ici](/blog/linux/integrer-icloud-gnome-calendar).
