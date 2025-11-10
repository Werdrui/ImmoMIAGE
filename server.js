const fs = require("fs");
const http = require("http");
const { url } = require("inspector");
const { type } = require("os");
const host = 'localhost';
const port = 8080;
const server = http.createServer();

server.on("request", (req, res) => {

    if (req.url.startsWith('/css/')){
        try {
            res.end(fs.readFileSync('.' + req.url));
        } catch (err) {
            console.log(err);
            res.end('erreur!');
    
    
    }} else if (req.url.startsWith('/images/')){
        try {
            res.end(fs.readFileSync('.' + req.url));
        } catch (err) {
            console.log(err);
            res.end('erreur!');
    
    
    }} else if(req.url == '/prix'){ // arrivée sur le site
        //let all_images = fs.readdirSync('./static/');

        // on construit la page HTML en dynamique
        let html = `
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Une maison ? Un prix !</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <a href="/index">Retour vers accueil</a><br>
        <!--<img width="200" height="200" src="/static/cabane.jpg">-->
        <h1>Un logement ? Un prix !</h1><br>

        <form id="collecte" method="GET" action="/resultat">

            <div>
            <label for="nb_metres">Nb mètres carrés :</label>
            <input type="number" id="nb_metres" name="nb_metres" min="0"><br>
            </div>

            <div>
            <label for="annee">Année de construction :</label>
            <input type="number" id="annee" name="annee"><br>
            </div>

            <div>
            <label>Type de logement :</label><br>
            <input type="radio" id="maison" name="type_logement" value="maison">
            <label for="maison">Maison</label>
            <input type="radio" id="appartement" name="type_logement" value="appartement">
            <label for="appartement">Appartement</label><br>
            </div>

            <div>
            <label for="nb_pieces">Nombre de pièces :</label>
            <input type="number" id="nb_pieces" name="nb_pieces" min="0"><br>
            </div>

            <div>
            <label for="nb_metresjardin">Surface du jardin (m²) :</label>
            <input type="number" id="nb_metresjardin" name="nb_metresjardin" min="0"><br>
            </div>

            <div>
            <label for="nb_etages">Nombre d'étages :</label>
            <input type="number" id="nb_etages" name="nb_etages" min="0"><br>
            </div>

            <div>
            <label for="piscine">Piscine :</label>
            <input type="checkbox" id="piscine" name="piscine"><br>
            </div>

            <div>
            <label for="parking">Place de parking :</label>
            <input type="checkbox" id="parking" name="parking"><br>
            </div>

            <div>
            <label for="balconTerrasse">Un balcon ou une terrasse :</label>
            <input type="checkbox" id="balconTerrasse" name="balconTerrasse"><br>
            </div>


            <div>
            <label for="diagEnergetique">Diagnostique énergétique :</label>
            <select name="diagEnergetique" id="diagEnergetique">
            <option value="Inconnu">Inconnu</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
            <option value="F">F</option>
            </select>
            </div>

            <div>
            <label for="vestuste">Estimez la vetusté :</label>
            <select name="vestuste" id="vestuste">
            <option value="Inconnu">Inconnu</option>
            <option value="A">Très bon état</option>
            <option value="B">Bon état</option>
            <option value="C">Passable</option>
            <option value="D">Travaux obligatoires</option>
            </select>
            </div>

            <button type="submit">Envoyer</button>
        </form>
    
    </body>
    </html>
    `;

    res.end(html);

    } else if(req.url.startsWith('/resultat')){
        const fullUrl = new URL(req.url, `http://${req.headers.host}`);
        const params = fullUrl.searchParams;

        const nbMetres = Number(params.get('nbMetres')) || 0;
        const annee = Number(params.get('annee')) || 0;
        const typeLogement = params.get('typeLogement') || '';
        const nbPieces = Number(params.get('nbPieces')) || 0;
        const nbMetresJardin = Number(params.get('nbMetresJardin')) || 0;
        const nbEtages = Number(params.get('nbEtages')) || 0;
        const piscine = params.get('piscine') || 'non';
        const parking = params.get('parking') || 'non';
        const balconTerrasse = params.get('balconTerrasse') || '';
        const diagEnergetique = params.get('diagEnergetique') || '';
        const vestuste = params.get('vestuste') || '';

        let prix = nbMetres * 3000 + nbPieces * 100000 + nbMetresJardin * 500
        + nbEtages * 10000 + annee * 1100;

        if (typeLogement === 'appartement') prix += 50000;
        else prix += 100000;

        if (piscine === 'on') prix += 50000;
        if (parking === 'on') prix += 50000;
        if (balconTerrasse === 'oui') prix += 100000;

        switch (diagEnergetique) {
            case 'A': prix += 50000; break;
            case 'B': prix += 40000; break;
            case 'C': prix += 30000; break;
            case 'D': prix += 20000; break;
            case 'E': prix += 10000; break;
            default: prix += 1000;
        }

        switch (vestuste) {
            case 'A': prix += 50000; break;
            case 'B': prix += 35000; break;
            case 'C': prix += 20000; break;
            default: prix += 1000;
        }
              
    let html = `
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Résultat</title>
        <link rel="stylesheet" href="/css/style.css">
    </head>
    <body>
        <a href="/index">Retour vers accueil</a><br>
        <h1>Votre prix !</h1><br>

    <p class="text"> Le prix est de : </p>

    ${prix}

    </body>
    </html>
    `;
    res.end(html);


    } else{
        res.end(fs.readFileSync('./index.html'));
    }
});

server.listen(port, host, () => {
    console.log(`Server running at http://${host}:${port}/`);
});