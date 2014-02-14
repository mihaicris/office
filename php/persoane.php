<?php

include_once 'conexiune.php';

function verifica_existenta_persoana($id, $nume_persoana, $prenume_persoana, $id_companie_persoana)
{
// testeaza existenta  in baza de date
// daca se apeleaza cu $id null inseamna se testeaza un vanzator nou

    $data = array($nume_persoana, $prenume_persoana, $id_companie_persoana);
    $string = 'SELECT `id_persoana`
                FROM `persoane`
                WHERE (`nume_persoana` = ?
                   AND `prenume_persoana` = ?
                   AND `id_companie_persoana` = ?);';
    $query = interogare($string, $data);
    $result = $query->fetch();
    if ($result['id_persoana'] && $result['id_persoana'] != $id) {
        // daca exista un rezultat si acesta este diferit de $id atunci exista
        // daca $id este null (creare) atunci la orice rezultat care nu este null inseamna ca exista
        echo('exista');
        exit();
    };
    return;
}

function afiseaza_rezultate($query)
{
    echo '<table class="rezultate">';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Nume și prenume</th>';
    echo '<th>Companie</th>';
    echo '<th>E-mail</th>';
    echo '<th>Mobil</th>';
    echo "</tr>";
    for ($i = 0; $row = $query->fetch(); $i++) {
        echo '<tr>';
        echo '<td id="f' . $row['id_persoana'] . '"><span class="id">' . $row['id_persoana'] . '</span><span class="sosa actiune">a</span></td>';
        echo '<td class="nume">' . $row['nume_persoana'] . ' ' . $row['prenume_persoana'] . '</td>';
        echo '<td class="companie">' . $row['nume_companie'] . '</td>';
        echo '<td class="email"><a class="email" href="mailto:' . $row['email_persoana'] . '">' . $row['email_persoana'] . '</a></td>';
        echo '<td class="mobil">' . $row['mobil_persoana'] . '</td>';
        echo '</tr>';
    } //end for
    echo '</table>';
}

function afiseaza_numar_total($count)
{
    echo '<table>';
    echo '<tr>';
    echo '<td class="total">' . $count;
    if ($count == 1) {
        echo " persoană";
    } else {
        echo " persoane";
    }
    echo '</td>';
    echo '</tr>';
    echo '</table>';
}

if (isset($_POST["formular_creare"])) {
    //  Formular creeare persoana de contact noua
    ?>
    <h2>Creare persoană contact</h2>
    <form action="/" method="post">
        <input id="id_persoana" type="hidden" name="id_persoana" value=""/>
        <table>
            <tbody>
            <tr>
                <td>
                    <label for="nume_persoana">Nume</label>
                    <input class="normal mediu"
                           id="nume_persoana"
                           type="text"
                           name="nume_persoana"
                           autocomplete="off"/>
                </td>
                <td>
                    <label for="prenume_persoana">Prenume</label>
                    <input class="normal mediu"
                           id="prenume_persoana"
                           type="text"
                           name="prenume_persoana"
                           autocomplete="off"/>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="tel_persoana">Telefon fix</label>
                    <input class="normal mediu"
                           id="tel_persoana"
                           type="text"
                           name="tel_persoana"
                           autocomplete="off"/>
                </td>
                <td>
                    <label for="fax_persoana">Fax</label>
                    <input class="normal mediu"
                           id="fax_persoana"
                           type="text"
                           name="fax_persoana"
                           autocomplete="off"/>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="mobil_persoana">Telefon Mobil</label>
                    <input class="normal mediu"
                           id="mobil_persoana"
                           type="text"
                           name="mobil_persoana"
                           autocomplete="off"/>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="email_persoana">Adresă de email</label>
                    <input class="normal mediu"
                           id="email_persoana"
                           type="text"
                           name="email_persoana"
                           autocomplete="off"/>
                </td>
                <td>
                    <label for="sex">Sex</label>
                    <select id="sex">
                        <option id="default" value=""></option>
                        <option value="0">Bărbat</option>
                        <option value="1">Femeie</option>
                    </select>
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="select_companie">Companie</label>
                    <input class="normal lung"
                           id="select_companie"
                           type="text"
                           name="select_companie"
                           data-id=""
                           autocomplete="off"/>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="dep_persoana">Departament</label>
                    <input class="normal mediu"
                           id="dep_persoana"
                           type="text"
                           name="dep_persoana"
                           autocomplete="off"/>
                </td>
                <td>
                    <label for="functie_persoana">Funcție</label>
                    <input class="normal mediu"
                           id="functie_persoana"
                           type="text"
                           name="functie_persoana"
                           autocomplete="off"/>
                </td>
                <td></td>
            </tr>
            </tbody>
        </table>
        <!--        <input id="id_companie"-->
        <!--               type="hidden"-->
        <!--               name="id_companie"-->
        <!--               value=""/>-->
        <a href="#" id="creaza_persoana" class="submit F1"><h3>Salvează<span class="sosa">å</span></h3></a>
        <a href="#" id="renunta" class="buton_renunta"><h3>Renunță<span class="sosa">ã</span></h3></a>
    </form>
    <div id="lista_companii" class="ddm"></div>
    <?php
    exit();
}
if (isset($_POST["formular_editare"])) {
    // editeaza persoana din baza de date
    $id = $_POST["id"];
    $string = 'SELECT P.*, C.nume_companie
				   FROM `persoane` AS P
				   LEFT JOIN companii AS C
				   ON P.id_companie_persoana = C.id_companie
				   WHERE P.id_persoana = ?
				   LIMIT 1;';
    $data = array($id);
    $query = interogare($string, $data);
    $row = $query->fetch(PDO::FETCH_ASSOC);
    if (count($row) == 1) {
        echo("Inexistent");
        exit();
    }
    ?>
    <h2>Modificare persoană de contact</h2>
    <form action="/" method="post">
        <input id="id_persoana" type="hidden" name="id_persoana" value="<?php echo $row['id_persoana']; ?>"/>
        <table>
            <tbody>
            <tr>
                <td>
                    <label for="nume_persoana">Nume</label>
                    <input class="normal mediu" id="nume_persoana" type="text" name="nume_persoana"
                           value="<?php echo $row['nume_persoana']; ?>" autocomplete="off"/>
                </td>
                <td>
                    <label for="prenume_persoana">Prenume</label>
                    <input class="normal mediu" id="prenume_persoana" type="text" name="prenume_persoana"
                           value="<?php echo $row['prenume_persoana']; ?>" autocomplete="off"/>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="tel_persoana">Telefon fix</label>
                    <input class="normal mediu" id="tel_persoana" type="text" name="tel_persoana"
                           value="<?php echo $row['tel_persoana']; ?>" autocomplete="off"/>
                </td>
                <td>
                    <label for="fax_persoana">Fax</label>
                    <input class="normal mediu" id="fax_persoana" type="text" name="fax_persoana"
                           value="<?php echo $row['fax_persoana']; ?>" autocomplete="off"/>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="mobil_persoana">Telefon Mobil</label>
                    <input class="normal mediu" id="mobil_persoana" type="text" name="mobil_persoana"
                           value="<?php echo $row['mobil_persoana']; ?>" autocomplete="off"/>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="email_persoana">Adresă de email</label>
                    <input class="normal mediu" id="email_persoana" type="text" name="email_persoana"
                           value="<?php echo $row['email_persoana']; ?>" autocomplete="off"/>
                </td>
                <td>
                    <label for="sex">Sex</label>
                    <select id="sex">
                        <option value="0" <?php if (!$row['gen_persoana']) echo 'selected="selected"' ?>>Bărbat
                        </option>
                        <option value="1" <?php if ($row['gen_persoana']) echo 'selected="selected"' ?>>Femeie
                        </option>
                    </select>
                </td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="select_companie">Companie</label>
                    <input class="normal mediu select_companie"
                           id="select_companie"
                           type="text"
                           name="select_companie"
                           value="<?php echo $row['nume_companie']; ?>"
                           data-id="<?php echo $row['id_companie_persoana']; ?>"
                           autocomplete="off"/>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label for="dep_persoana">Departament</label>
                    <input class="normal mediu" id="dep_persoana" type="text" name="dep_persoana"
                           value="<?php echo $row['departament_persoana']; ?>" autocomplete="off"/>
                </td>
                <td>
                    <label for="functie_persoana">Funcție</label>
                    <input class="normal mediu" id="functie_persoana" type="text" name="functie_persoana"
                           value="<?php echo $row['functie_persoana']; ?>" autocomplete="off"/>
                </td>
                <td></td>
            </tr>
            </tbody>
        </table>
        <a href="#" id="editeaza_persoana" class="submit F1"><h3>Modifică<span class="sosa">å</span></h3></a>
        <a href="#" id="sterge" class="buton_stergere"><h3>Șterge<span class="sosa">ç</span></h3></a>
        <a href="#" id="renunta" class="buton_renunta"><h3>Renunță<span class="sosa">ã</span></h3></a>
    </form>
    <div id="lista_companii" class="ddm"></div>
    <?php
    exit();
}
if (isset($_POST["salveaza"])) {
    $data = $_POST["formdata"];
    if ($_POST["salveaza"]) { // 1-creaza | 0-modifica
        verifica_existenta_persoana(null, $data[1], $data[2], $data[8]);
        $string = 'INSERT INTO `persoane`
					(`nume_persoana`,
					`prenume_persoana`,
					`tel_persoana`,
					`fax_persoana`,
					`mobil_persoana`,
					`email_persoana`,
					`gen_persoana`,
					`id_companie_persoana`,
					`departament_persoana`,
					`functie_persoana`,
					`id_persoana`)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL);';
        array_shift($data);
    } else {
        verifica_existenta_persoana($data[0], $data[1], $data[2], $data[8]); // data[0] contine id-ul persoanei care se modifica
        $string = 'UPDATE `persoane`
                   	    SET	`nume_persoana` = ?,
							`prenume_persoana` = ?,
							`tel_persoana` = ?,
							`fax_persoana` = ?,
							`mobil_persoana` = ?,
							`email_persoana` = ?,
							`gen_persoana` = ?,
							`id_companie_persoana` = ?,
							`departament_persoana` = ?,
							`functie_persoana`= ?
                       	WHERE `id_persoana` = ?;';
        array_push($data, array_shift($data));
    }
    $query = interogare($string, $data);
    echo('ok');
    exit();

}
if (isset($_POST["sterge"])) {
    // actiune stergere companiein baza de date
    $string = 'DELETE FROM `persoane` WHERE `id_persoana` = ? LIMIT 1;';
    $data = array($_POST['id']);
    $query = interogare($string, $data);
    echo('ok');
    exit();
}
if (isset($_POST["select_persoana"])) {

    // alegere persoana din baza de date in formulare

    $str = $_POST["id_companie"];

    // prima interogare pentru numarare rezultate
    $string = 'SELECT COUNT(*)
					FROM `persoane`
					WHERE `id_companie_persoana` = ?
					ORDER BY `nume_persoana` ASC
					LIMIT 1;';
    $data = array($str);
    $query = interogare($string, $data);
    //daca nu sunt rezultate se iese cu mesaj
    $count = $query->fetchColumn();
    if (!$count) {
        echo '<p class="noresults">
			        <strong>Nu există în baza de date.</strong>
			        <br/>Crează persoana din meniul
			        <span class="sosa_font" style="color: #000">ñ</span><em>Administrare, Persoane</em>
			       </p>';
        exit();
    }
    // interogarea adevarata pentru rezultate
    $string = 'SELECT *
					FROM `persoane`
					WHERE `id_companie_persoana` = ?
					ORDER BY `nume_persoana` ASC;';
    $query = interogare($string, $data);
//    echo '<div class="rec" id="source"><p>' . $_POST["select_persoana"] . '</p></div>';
    for ($i = 0; $row = $query->fetch(); $i++) {
        echo '<div class="rec">';
        echo '<p  id="f' . $row['id_persoana'] . '" class="bold">';
        if ($row['gen_persoana']) {
            echo "Dna. ";
        } else {
            echo "Dl. ";
        }
        echo $row['nume_persoana'];
        echo '&nbsp' . $row['prenume_persoana'] . "</p>";
        echo '<p style="color: #3F41D9;">' . $row['functie_persoana'] . '</p>';
        echo '</div>';
    }
    exit();
}
if (isset($_POST["camp_str"])) {
    // cautare persoane in baza de date
    $str = "%" . $_POST["camp_str"] . "%";
    // prima interogare pentru numar de rezultate
    $data = array($str, $str, $str, $str, $str, $str);
    $string = 'SELECT COUNT(*)
					FROM persoane AS P
					LEFT JOIN companii AS C ON P.id_companie_persoana = C.id_companie
					WHERE (nume_persoana LIKE ?
						OR prenume_persoana LIKE ?
						OR functie_persoana LIKE ?
						OR departament_persoana LIKE ?
						OR email_persoana LIKE ?
						OR nume_companie LIKE ?)
					ORDER BY nume_persoana ASC LIMIT 1;';
    $query = interogare($string, $data);
    $count = $query->fetchColumn();
    if (!$count) { //daca nu sunt rezultate se iese cu mesaj
        // echo "<h2>Rezultate căutare</h2>";
        echo '<p>Nu există în baza de date.</p>';
        exit();
    }
    if ($str == '%%') {
        afiseaza_numar_total($count);
        exit();
    }
    // interogarea adevarata pentru rezultate (daca nu s-a iesit mai sus)
    $string = 'SELECT P.* , C.nume_companie
				FROM persoane AS P
				LEFT JOIN companii AS C ON P.id_companie_persoana = C.id_companie
				WHERE (nume_persoana LIKE ?
				    OR prenume_persoana LIKE ?
					OR functie_persoana LIKE ?
					OR departament_persoana LIKE ?
					OR email_persoana LIKE ?
					OR nume_companie LIKE ?)
				ORDER BY nume_persoana ASC;';
    $query = $db->prepare($string);
    $query->execute($data);
    afiseaza_rezultate($query);
    afiseaza_numar_total($count);
    exit();
}
?>
    <h2>Lista persoane de contact</h2>
    <form action="/" method="post" id="submit">
        <label for="camp">Caută</label>
        <input id="camp" class="normal mediu" type="text" name="camp_str" autocomplete="off"/>
        <a href="#" id="produs_nou" class="submit nou">
            <h3>Crează un contact nou</h3>
        </a>
    </form>
<?php
$string = 'SELECT COUNT(*)
           FROM `persoane`
           LIMIT 1;';
$query = interogare($string, null);
$count = $query->fetchColumn();
if (!$count) { //daca nu sunt rezultate se iese cu mesaj
    // echo "<h2>Rezultate căutare</h2>";
    echo '<p>Nu există în baza de date.</p>';
    exit();
}
afiseaza_numar_total($count);
?>