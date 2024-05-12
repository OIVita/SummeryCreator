<?php
function generaSommario($capitoli) {
    $colonne = 60;
    $sommario = '';
    $numeroCapitolo = 1;
    foreach ($capitoli as $capitolo) {
        $titolo = $capitolo[0];
        $pagina = $capitolo[1];
        $righeTitolo = explode("\n", wordwrap($titolo, $colonne, "\n", true));
        foreach ($righeTitolo as $indice => $riga) {
            $numeroCapitoloFormattato = sprintf("%' 2d", $numeroCapitolo);
            $paginaFormattata = ($indice === 0) ? sprintf("pag. %' 3d", $pagina) : '';
            $rigaFormattata = ($indice === 0) ? "Capitolo $numeroCapitoloFormattato - $riga" : str_repeat(' ', mb_strlen("Capitolo $numeroCapitoloFormattato - ")) . $riga;
            $spaziVuoti = $colonne - mb_strlen($riga);
            $spazi = str_repeat('.', $spaziVuoti);
            $sommario .= "$rigaFormattata $spazi $paginaFormattata\n";
        }
        $numeroCapitolo++;
    }
    return $sommario;
}

function generaSommarioDaJSON($jsonFile, $colonne) {
    $jsonContent = file_get_contents($jsonFile);
    $capitoli = json_decode($jsonContent, true);
    $sommario = '';
    $numeroCapitolo = 1;
    foreach ($capitoli as $capitolo) {
        $titolo = $capitolo['titolo'];
        $pagina = $capitolo['pagina'];
        $righeTitolo = explode("\n", wordwrap($titolo, $colonne, "\n", true));
        foreach ($righeTitolo as $indice => $riga) {
            $numeroCapitoloFormattato = sprintf("%' 2d", $numeroCapitolo);
            $paginaFormattata = ($indice === 0) ? sprintf("pag. %' 3d", $pagina) : '';
            $rigaFormattata = ($indice === 0) ? "Capitolo $numeroCapitoloFormattato - $riga" : str_repeat(' ', mb_strlen("Capitolo $numeroCapitoloFormattato - ")) . $riga;
            $spaziVuoti = $colonne - mb_strlen($riga);
            $spazi = str_repeat('.', $spaziVuoti);
            $sommario .= "$rigaFormattata $spazi $paginaFormattata\n";
        }
        $numeroCapitolo++;
    }
    return $sommario;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Sommario Libro</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        .wrapper {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }

        #sommarioForm,
        #sommarioVisualizzato {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-sizing: border-box;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        label {
            display: inline-block;
            margin-bottom: 8px;
        }

        input[type="number"],
        input[type="text"] {
            width: 50px;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: #ffffff;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        #sommarioVisualizzato {
            margin:0 auto;
            white-space: pre-wrap;
            width: fit-content;
            max-width: 100%;
        }
        .info {
            background-color: #f2f2f2;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.5;
        }

        .bold {
            font-weight: bold;
        }

        .italic {
            font-style: italic;
        }

        .underline {
            text-decoration: underline;
        }

        .highlight {
            color: #007bff;
        }

        .bottoni {
            margin-top: 20px;
            text-align: center;
        }
        .titoloJus{width: calc(100% - 400px)!important;}
    </style>
    
<script>
    function aggiungiCapitolo() {
        const capitoliContainer = document.getElementById('capitoliContainer');
        const nuovoCapitolo = document.createElement('div');
        nuovoCapitolo.classList.add('capitolo');
        nuovoCapitolo.innerHTML = `
            <label>Capitolo:</label>
            <input type="number" name="numeroCapitolo[]" min="1" required>
            <label>Titolo:</label>
            <input type="text" name="titoloCapitolo[]" required>
            <label>Pagina:</label>
            <input type="number" name="paginaCapitolo[]" min="1" required>
            <button type="button" onclick="rimuoviCapitolo(this)">Rimuovi</button>
        `;
        capitoliContainer.appendChild(nuovoCapitolo);
    }

    function copiaSommario() {
        const sommarioVisualizzato = document.getElementById('sommarioVisualizzato');
        const selezione = window.getSelection();
        const range = document.createRange();
        range.selectNodeContents(sommarioVisualizzato);
        selezione.removeAllRanges();
        selezione.addRange(range);
        document.execCommand('copy');
        selezione.removeAllRanges();
    }
    function rimuoviCapitolo(btn) {
        const capitoloDaRimuovere = btn.parentNode;
        capitoloDaRimuovere.remove();
    }
</script>
</head>
<body>
<div class="wrapper">
    <div class="info">
        <span class="bold italic underline highlight">Benvenuto nella pagina di generazione di sommari per libri.</span> <br>Qui puoi caricare un file JSON contenente i capitoli del tuo libro e generare un sommario formattato. Inserisci il numero di colonne desiderato e scegli il file JSON dal tuo dispositivo. Premi il pulsante "<span class="highlight">Carica e Genera Sommario</span>" per visualizzare il sommario nel riquadro sottostante. Se desideri inserire i capitoli manualmente, puoi farlo nel secondo modulo. Ogni capitolo richiede un numero, un titolo e il numero di pagina. Aggiungi nuovi capitoli premendo il pulsante "<span class="highlight">Aggiungi capitolo</span>" e, una volta completato, premi "<span class="highlight">Salva</span>" per visualizzare il sommario. Una volta generato il sommario, puoi copiarlo premendo il pulsante "<span class="highlight">Copia sommario</span>". Grazie per utilizzare il nostro servizio!
    </div>
    <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['jsonFile']) && $_FILES['jsonFile']['error'] === UPLOAD_ERR_OK) {
            $tmpFilePath = $_FILES['jsonFile']['tmp_name'];
            $destFilePath = 'uploaded_json/' . $_FILES['jsonFile']['name'];
            $fileExtension = pathinfo($_FILES['jsonFile']['name'], PATHINFO_EXTENSION);
            if ($fileExtension !== 'json') {
                echo "Errore: Assicurati di caricare un file con estensione JSON.";
                exit();
            }
            $jsonContent = file_get_contents($tmpFilePath);
            $decodedJson = json_decode($jsonContent, true);
            if ($decodedJson === null && json_last_error() !== JSON_ERROR_NONE) {
                echo "Errore: Il file caricato non è un JSON valido.";
                exit();
            }
            if (!is_array($decodedJson)) {
                echo "Errore: Il contenuto del JSON deve essere un array.";
                exit();
            }
            foreach ($decodedJson as $capitolo) {
                if (!isset($capitolo['titolo']) || !isset($capitolo['pagina']) || empty($capitolo['titolo']) || empty($capitolo['pagina'])) {
                    echo "Errore: Ogni elemento del JSON deve avere le chiavi 'titolo' e 'pagina' con valori non vuoti.";
                    echo '<div class="bottoni">';
                    echo '<button type="reset" onclick="window.location.href = window.location.href">Reset</button>';
                    echo '</div>';
                    exit();
                }
            }
            if (move_uploaded_file($tmpFilePath, $destFilePath)) {
                $numeroColonne = isset($_POST['numeroColonne']) ? $_POST['numeroColonne'] : 60;
                $sommario = generaSommarioDaJSON($destFilePath, $numeroColonne);
                echo "<pre id='sommarioVisualizzato'>$sommario</pre>";
                echo '<div class="bottoni">';
                echo '<button type="button" onclick="copiaSommario()">Copia sommario</button>';
                echo '<button type="reset" onclick="window.location.href = window.location.href">Reset</button>';
                echo '</div>';
            } else {
                echo "Si è verificato un errore durante il caricamento del file.";
            }
            exit();
        }
        elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numeroColonne']) && isset($_POST['numeroCapitolo']) && isset($_POST['titoloCapitolo']) && isset($_POST['paginaCapitolo'])) {
            $numeroColonne = $_POST['numeroColonne'];
            $numeriCapitoli = $_POST['numeroCapitolo'];
            $titoliCapitoli = $_POST['titoloCapitolo'];
            $pagineCapitoli = $_POST['paginaCapitolo'];
            $capitoli = [];
            if(count($numeriCapitoli) === count($titoliCapitoli) && count($numeriCapitoli) === count($pagineCapitoli)) {
                for ($i = 0; $i < count($numeriCapitoli); $i++) {
                    $capitoli[] = [$titoliCapitoli[$i], $pagineCapitoli[$i]];
                }
                $sommario = generaSommario($capitoli);
                echo "<pre id='sommarioVisualizzato'>$sommario</pre>";
                echo '<div class="bottoni">';
                echo '<button type="button" onclick="copiaSommario()">Copia sommario</button>';
                echo '<button type="reset" onclick="window.location.href = window.location.href">Reset</button>';
                echo '</div>';
            } else {
                echo "Errore: Assicurati di compilare tutti i campi relativi ai capitoli.";
            }
        }

    ?>

    <form id="sommarioForm" method="post" enctype="multipart/form-data">
    <label for="numeroColonne">Colonne:</label>
    <input type="number" id="numeroColonne" name="numeroColonne" value="60" min="1">
        <input class="scegli" type="file" name="jsonFile" accept=".json">
        <button type="submit">Carica e Genera Sommario</button>
    </form>

    <form id="sommarioForm" method="post">
        <label for="numeroColonne">Colonne:</label>
        <input type="number" id="numeroColonne" name="numeroColonne" value="60" min="1">

        <div id="capitoliContainer">
            <div class="capitolo">
                <label>Capitolo:</label>
                <input type="number" name="numeroCapitolo[]" min="1" required>
                <label>Titolo:</label>
                <input class="titoloJus" type="text" name="titoloCapitolo[]" required>
                <label>Pagina:</label>
                <input type="number" name="paginaCapitolo[]" min="1" required>
                <button type="button" onclick="rimuoviCapitolo(this)">Rimuovi</button>
            </div>
        </div>

        <button type="button" onclick="aggiungiCapitolo()">Aggiungi capitolo</button>
        <button type="submit">Genera Sommario</button>
    </form>
</div>
</body>
</html>
