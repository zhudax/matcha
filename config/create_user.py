#coding: utf-8

import random as r
import time
from collections import OrderedDict

class CreateUsers():
    number_user = 600 + 1
    usersfile = "seed/users.csv"
    profilfile = "seed/profil.csv"
    adressefile = "seed/adresse.csv"
    viewfile = "seed/view.csv"
    likefile = "seed/likes.csv"
    matchfile = "seed/match.csv"
    imgfile = "seed/img.csv"
    tagfile = "seed/tags.csv"

    stock_like = OrderedDict()

    def get_nom(self):
        nom = ['Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard',
            'Petit', 'Durant', 'Leroy', 'Moreau', 'Simon', 'Laurent', 'Lefebvre',
            'Michel', 'Garcia', 'David', 'Bertrand', 'Roux', 'Vincent', 'Fournier',
            'Morel', 'Girard', 'Andre', 'Lefevre', 'Mercier', 'Dupont', 'Lambert',
            'Bonnet', 'Francois', 'Martinez', 'Legrand', 'Garnier']
        return nom[r.randint(0, len(nom) - 1)]

    def get_prenom(self, genre):
        prenom = OrderedDict()
        prenom["F"] = ['Juliette', 'Maeva', 'Jeanne', 'Lucie', 'Noemie', 'Alice',
            'Alicia', 'Eloise', 'Angele', 'Amelie', 'Chiara', 'Flora', 'Julia',
            'Eleonore', 'Cassandra', 'Myriam', 'Marion', 'Olivia', 'Rose', 'Maud',
            'Valentine', 'Alyssa', 'Chaima', 'Aya', 'Anaelle', 'Lea', 'Eva',
            'Lena', 'Julie', 'Manon', 'Zoe', 'Anna', 'Clementine', 'Alexia',
            'Axelle', 'Claire', 'Gabrielle', 'Charline', 'Elisa', 'Clarisse',
            'Lucile', 'Heloise', 'Fanny', 'Salome', 'Leonie', 'Melanie']
        prenom["M"] = ['Enzo', 'Adrien', 'Antoine', 'Gabriel', 'Valentin', 'Louis',
            'Theo', 'Matheo', 'Augustin', 'Diego', 'Erwan', 'Bastien', 'Alban',
            'Gabin', 'Jeremy', 'Etienne', 'Luca', 'Morgan', 'Gaspard', 'Kilian',
            'Loic', 'Thibault', 'Tiago', 'Wiliam', 'Robin', 'Lilian', 'Mathis',
            'Baptiste', 'Evan', 'Julien', 'Maxime', 'Raphael', 'Tom', 'Kylian',
            'Arnaud', 'Emilien', 'Charles']
        return prenom[genre][r.randint(0, len(prenom[genre]) - 1)]

    def get_genre(self):
        genre = ["F","M"]
        return genre[r.randint(0, 1)]

    def get_orient_sex(self, genre):
        orient = {}
        orient["F"] = ['M', 'M', 'M', 'M', 'M', 'M', 'M', 'F', 'M', 'M', 'M', 'M', 'M', 'M', 'M', 'M', 'M', 'M', 'M', 'M']
        orient["M"] = ['F', 'F', 'F', 'M', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F', 'F']
        return orient[genre][r.randint(0, len(orient[genre]) - 1)]

    def get_birthday(self):
        younger = time.gmtime()[0]
        older = younger - 40
        year = [i for i in range(older, younger - 18)]
        month = [i for i in range(1, 13)]
        day = [i for i in range(1, 32)]
        while True:
            birthday = str(year[r.randint(0, len(year) - 1)]) + "-"
            birthday += str(month[r.randint(0, len(month) - 1)]) + "-"
            birthday += str(day[r.randint(0, len(day) - 1)])
            try:
                time.strptime(birthday, "%Y-%m-%d")
            except ValueError:
                pass
            else:
                break
        return birthday

    def get_age(self, birthday):
        (uyear, umonth, uday) = birthday.split('-')
        (uyear, umonth, uday) = (int(uyear), int(umonth), int(uday))
        current = time.gmtime()
        (year, month, day) = (current[0], current[1], current[2])
        if ((umonth < month) or ((umonth == month) and (uday <= day))):
            age = year - uyear
        else:
            age = year - uyear - 1
        return age

    def get_adresse(self):
        adresse = [
            {'num': "22", 'rue': "Boulevard de Courcelles", 'ville': "Paris", 'region': "Île-de-France", 'cp': "75017", 'pays': "France", 'latitue': 48.88112899999999, 'longitude': 2.313079000000016},
            {'num': "39", 'rue': "Avenue de Paris", 'ville': "Soissons", 'region': "Hauts-de-France", 'cp': "02200", 'pays': "France", 'latitue': 49.3778401, 'longitude': 3.31669880000004},
            {'num': "34", 'rue': "Rue Maréchal de Lattre de Tassigny", 'ville': "Saint-Dizier", 'region': "Grand Est", 'cp': "52100", 'pays': "France", 'latitue': 48.6383308, 'longitude': 4.949663299999997},
            {'num': "119", 'rue': "Boulevard Haussmann", 'ville': "Paris", 'region': "Île-de-France", 'cp': "75008", 'pays': "France", 'latitue': 48.87515080000001, 'longitude': 2.3153328999999303},
            {'num': "28", 'rue': "Rue Anatole le Braz", 'ville': "Carhaix-Plouguer", 'region': "Bretagne", 'cp': "29270", 'pays': "France", 'latitue': 48.2756653, 'longitude': -3.5684443999999758},
            {'num': "8", 'rue': "Rue Dauphine", 'ville': "Dijon", 'region': "Bourgogne Franche-Comté", 'cp': "21000", 'pays': "France", 'latitue': 47.3209657, 'longitude': 5.039229900000009},
            {'num': "11c", 'rue': "Avenue Charles de Gaulle", 'ville': "Boissy-Saint-Léger", 'region': "Île-de-France", 'cp': "94470", 'pays': "France", 'latitue': 48.7513665, 'longitude': 2.5007473000000573},
            {'num': "124", 'rue': "Avenue Maréchal de Saxe", 'ville': "Lyon", 'region': "Auvergne-Rhône-Alpes", 'cp': "69003", 'pays': "France", 'latitue': 45.7568574, 'longitude': 4.846179200000051},
            {'num': "125", 'rue': "Boulevard Haussmann", 'ville': "Paris", 'region': "Île-de-France", 'cp': "75008", 'pays': "France", 'latitue': 48.8751063, 'longitude': 2.314140400000042},
            {'num': "116", 'rue': "Rue Baudelaire", 'ville': "Lunel", 'region': "Occitanie", 'cp': "34400", 'pays': "France", 'latitue': 43.6716626, 'longitude': 4.141969300000028},
            {'num': "5", 'rue': "Rue Louis Braille", 'ville': "Montauban", 'region': "Occitanie", 'cp': "82000", 'pays': "France", 'latitue': 44.0203342, 'longitude': 1.3688918000000285},
            {'num': "55", 'rue': "Avenue Jean-Marsaudon", 'ville': "Savigny-sur-Orge", 'region': "Île-de-France", 'cp': "91600", 'pays': "France", 'latitue': 48.6947876, 'longitude': 2.351593600000001},
            {'num': "3", 'rue': "Rue du Docteur Bézy", 'ville': "Sainte-Livrade-sur-Lot", 'region': "Nouvelle-Aquitaine", 'cp': "47110", 'pays': "France", 'latitue': 44.3981053, 'longitude': 0.5900348999999778},
            {'num': "6", 'rue': "Rue Beauséjour", 'ville': "Les Epesses", 'region': "Pays de la Loire", 'cp': "85590", 'pays': "France", 'latitue': 46.8823208, 'longitude': -0.9010865000000194},
            {'num': "13", 'rue': "Rue Eugène Feautrier", 'ville': "La Roche-Bernard", 'region': "Bretagne", 'cp': "56130", 'pays': "France", 'latitue': 47.5203986, 'longitude': -2.2995352000000366},
            {'num': "144", 'rue': "Boulevard des États-Unis", 'ville': "Lyon", 'region': "Auvergne-Rhône-Alpes", 'cp': "69008", 'pays': "France", 'latitue': 45.7264347, 'longitude': 4.869007499999952},
            {'num': "4", 'rue': "Rue Saint-Thomas", 'ville': "Saint-Lô", 'region': "Normandie", 'cp': "50000", 'pays': "France", 'latitue': 49.1139197, 'longitude': -1.0910819000000629},
            {'num': "999", 'rue': "Rue Louis Blériot", 'ville': "Buc", 'region': "Île-de-France", 'cp': "78530", 'pays': "France", 'latitue': 48.7820449, 'longitude': 2.131774899999982},
            {'num': "17", 'rue': "Rue des Camélias", 'ville': "Lannion", 'region': "Bretagne", 'cp': "22300", 'pays': "France", 'latitue': 48.7452669, 'longitude': -3.4683416000000307},
            {'num': "19", 'rue': "Rue Henry Chéron", 'ville': "Lisieux", 'region': "Normandie", 'cp': "14100", 'pays': "France", 'latitue': 49.1460631, 'longitude': 0.2281975999999304}
        ]
        return adresse[r.randint(0, len(adresse) - 1)]

    def get_bio(self):
        bio = ["bio1", "bio2", "bio3"]
        return bio[r.randint(0, len(bio) - 1)]

    def get_tag(self):
        tag = ['argent', 'arts', 'automobile', 'cuisine', 'diriger', 'droit',
            'ecrire', 'esthetique', 'flore', 'informatique', 'outils', 'organiser',
            'theatre', 'cinema', 'lire', 'langues', 'dessiner', 'ecologie',
            'benevolat', 'faune', 'mecanique', 'plein air', 'sciences', 'sports',
            'electricite', 'animaux', 'biologie', 'ecouter', 'reparer', 'physique',
            'nature', 'decoration', 'militaire', 'recherche', 'plein air',
            'hospitalier', 'musique', 'chiffres', 'mathematique', 'svt', 'nourriture',
            'voyage', 'construire', 'france', 'geographie', 'philosophie', 'histoire',
            'jeux', 'wii', 'mac', 'food', 'shopping', 'femme', 'homme', 'mode', '42',
            'piscine', 'piscine42']
        return "#" + tag[r.randint(0, len(tag) - 1)]

    def match(self, user1, user2):
        end = ",\n"
        with open(self.matchfile, 'a') as mfile:
            match = "(%d, %d)%s" % (user1, user2, end)
            mfile.write(match)

    def check_match(self):
        for user in range(self.number_user):
            for uid, likes in self.stock_like.items():
                if (user < uid) and (user in likes) and (uid in self.stock_like[user]):
                    self.match(user, uid)

    def get_nblike(self, user, view):
        while True:
            nblike = r.randint(1, self.number_user // 2)
            if (nblike <= view):
                break

        with open(self.likefile, 'a') as lfile:
            self.stock_like[user] = []
            for i in range(nblike):
                end = ",\n" if (user != self.number_user - 1) or (i != nblike - 1) else ""
                while True:
                    uid = r.randint(1, self.number_user - 1)
                    if uid not in self.stock_like[user] and uid != user:
                        break
                self.stock_like[user].append(uid)
                likes = "(%d, %d)%s" % (uid, user, end)
                lfile.write(likes)
        return nblike

    def get_nbview(self, user):
        nbview = r.randint(1, (self.number_user // 2) + (self.number_user // 4))
        left = self.number_user - 1 - user
        src = []

        with open(self.viewfile, 'a') as vfile:
            for i in range(nbview):
                end = ",\n" if left or (i != nbview - 1) else ""
                while True:
                    uid = r.randint(1, self.number_user - 1)
                    if uid not in src and uid != user:
                        break
                src.append(uid);
                view = "(%d, %d)%s" % (uid, user, end)
                vfile.write(view)
        return nbview

    def set_tags(self, user):
        number_tag = r.randint(1, 10)
        tags = []

        with open(self.tagfile, 'a') as tfile:
            for i in range(number_tag):
                end = ",\n" if (user != self.number_user - 1) or (i != number_tag - 1) else ""
                while True:
                    tag = self.get_tag()
                    if tag not in tags:
                        break
                tags.append(tag)
                tag = "(%d, '%s')%s" % (user, tag, end)
                tfile.write(tag)

    def get_img_profil(self, genre, user):
        img_dir = {}
        img_dir["F"] = "app/public/img/profil/feminin/f"
        img_dir["M"] = "app/public/img/profil/masculin/m"
        num_profil = r.randint(1, 16);
        photo_dir = "app/public/img/photos/"
        photos = []
        number_photos = list(range(r.randint(1, 4)))
        for i in number_photos:
            photos.append(r.randint(1, 30))
        end = ",\n"
        with open(self.imgfile, 'a') as ifile:
            path = img_dir[genre] + str(num_profil) + ".jpg"
            img_profil = "(%d, '%s', 'Y')%s" % (user, path, end)
            ifile.write(img_profil)
            for i in number_photos:
                end = ", \n" if user != self.number_user - 1 or i != number_photos[-1] else ""
                path = photo_dir + str(photos[i]) + ".jpeg"
                img_photo =  "(%d, '%s', 'N')%s" % (user, path, end)
                ifile.write(img_photo)

    def get_info_user(self, genre, i):
        user = OrderedDict()
        ext_email = ["42.fr", "hotmail.com", "yahoo.fr", "outlook.com", "gmail.com"]
        user["login"] = "user" + str(i)
        user["nom"] = self.get_nom().title()
        user["prenom"] = self.get_prenom(genre).title()
        user["email"] = user["prenom"].lower() + "_" + user["nom"].lower() + "@" + ext_email[r.randint(0, len(ext_email) - 1)]
        #whirlpool Matcha42
        user["password"] = "5faba8ad766dec5582aa91561c33375e8d6e0797f71bd91f027104e67e97032bc0dbb65d4f7e34444417a94f3c7166c151d54d3bc96ef18a2f4b1578712ecc80"
        user["cle"] = "83c1f8ac7c93c5db608d08cf2d5ea3035bbffa9ee747c2ec87c8b8b33cf36cae17f516e7359684985db3b7c2e2c41a089ade4c753f3837c6358fdf681713e8a2"
        end = ",\n" if i != self.number_user - 1 else ""
        users = "(%d, '%s', '%s', '%s', '%s', '%s', '%s', 'Y')%s" % \
                (i, user['nom'], user['prenom'], user['login'], \
                user['password'], user['email'], user['cle'], end)
        with open(self.usersfile, 'a') as ufile:
            ufile.write(users)

    def get_info_profil(self, genre, i):
        profil = OrderedDict()
        profil["orient_sex"] = self.get_orient_sex(genre)
        profil["birthday"] = self.get_birthday()
        profil["age"] = self.get_age(profil["birthday"])
        profil["bio"] = self.get_bio()
        profil["nbview"] = self.get_nbview(i)
        profil["nblike"] = self.get_nblike(i, profil["nbview"])
        end = ",\n" if i != self.number_user - 1 else ""
        pro = "(%d, '%s', '%s', %d, '%s', '%s', %d, %d)%s" % \
            (i, genre, profil['orient_sex'], profil['age'], profil['birthday'], \
            profil['bio'], profil['nblike'], profil['nbview'], end)
        with open(self.profilfile, 'a') as pfile:
            pfile.write(pro)

    def get_info_adresse(self, i):
        adr = self.get_adresse()
        end = ",\n" if i != self.number_user - 1 else ""
        adresse = "(%d, '%s', '%s', '%s', '%s', '%s', '%s', %lf, %lf)%s" % \
            (i, adr["pays"], adr["region"], adr["ville"], adr["cp"], adr["rue"], \
            adr["num"], adr["latitue"], adr["longitude"], end)
        with open(self.adressefile, 'a') as afile:
            afile.write(adresse)

    def get_info(self, i):
        genre = self.get_genre()
        self.get_info_user(genre, i)
        self.get_info_profil(genre, i)
        self.get_info_adresse(i)
        self.get_img_profil(genre, i)
        self.set_tags(i)

    def stock_info(self):
        number_user = self.number_user
        for i in range(1, number_user):
            self.get_info(i)
        self.check_match()
        with open(self.matchfile) as mfile:
            tmp = mfile.read()
        with open(self.matchfile, 'w') as mfile:
            mfile.write(tmp[:-2])

    def clear_file(self):
        open(self.viewfile, 'w').close()
        open(self.likefile, 'w').close()
        open(self.imgfile, 'w').close()
        open(self.matchfile, 'w').close()
        open(self.usersfile, 'w').close()
        open(self.profilfile, 'w').close()
        open(self.adressefile, 'w').close()
        open(self.tagfile, 'w').close()

    def create_users(self):
        self.clear_file()
        self.stock_info()

if __name__ == "__main__":
    c = CreateUsers()
    c.create_users()
