const fs = require("fs");
const http = require("http");
const { url } = require("inspector");
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
        <h1>Une maison ? Un prix !</h1><br>

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

            <button type="submit">Envoyer</button>
        </form>
    
    </body>
    </html>
    `;

    res.end(html);

    } else if(req.url.startsWith('/resultat')){
      const queryString = req.url.split('?')[1];
      let nbMetres, annee, typeLogement, nbPieces, nbMetresJardin, nbEtages, piscine;
      
      if(queryString){
        const params = queryString.split('&'); 

        const nbMetres = params[0] ? decodeURIComponent(params[0].split('=')[1]) : null;
        const annee = params[1]  ? decodeURIComponent(params[1].split('=')[1]) : null;
        const typeLogement = params[2]  ? decodeURIComponent(params[2].split('=')[1]) : null;
        const nbPieces = params[3]  ? decodeURIComponent(params[3].split('=')[1]) : null;
        const nbMetresJardin = params[4]  ? decodeURIComponent(params[4].split('=')[1]) : null;
        const nbEtages = params[5]  ? decodeURIComponent(params[5].split('=')[1]) : null;   
        const piscine = params[6]  ? decodeURIComponent(params[6].split('=')[1]) : null;
      }

      const prix = (parseInt(nbPieces || 0) + parseInt(nbMetresJardin || 0) + parseInt(nbEtages || 0) + parseInt(annee || 0));

              
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

    <p> Le prix est de : </p>

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