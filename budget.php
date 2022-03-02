<?php
  session_start();
  $email = $_SESSION['email'];

  include 'connect_mysql/connect.php';
	$conn = OpenCon();

  $sql    = "SELECT * FROM Customer WHERE EMail='$email'";
  $result = $conn->query($sql);
  $row    = $result->fetch_assoc();
  $id     = $row['CustomerID'];

  // Henter bilde
  $sqlimage     = "SELECT name FROM images WHERE CustomerID='$id'";
  $resultimage  = mysqli_query($conn,$sqlimage);
  $rowimage     = mysqli_fetch_array($resultimage);

  $image        = $rowimage['name'];
  $image_src    = "upload/".$image;

  // Gjør klar litt data for siden
  $sql = "SELECT * from budget WHERE customerID=" . $id .
    " AND YEAR(creationDate)=" . (int)date('Y') . 
    " AND MONTH(creationDate)=" . (int)date('m') . ";";

  $result   = $conn->query($sql);
  $row      = $result->fetch_assoc();
  $budgetID = $row["budgetID"];
?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <link rel="stylesheet" href="main.css">
    <title>Finance Budget App</title>
  </head>
  <body>


    <header>
        <div id="sideMenu">
          <div style="align-self: flex-start;">
            <a href="home.php"
              ><div class="block sideMenuItem">
                <img
                alt="side_menu_icon"
                  src="https://www.svgrepo.com/show/14443/home.svg"
                  class="sideMenuIcon"
                />Home
              </div></a
            >
            <a href="budget.php"
              ><div class="block sideMenuItem">
                <img
                  alt="side_menu_icon"
                  src="https://www.svgrepo.com/show/17167/pie-chart.svg"
                  class="sideMenuIcon"
                />Budget
              </div></a
            >
            <a href="budget-planner.php"
              ><div class="block sideMenuItem">
                <img
                  alt="side_menu_icon"
                  src="https://www.svgrepo.com/show/11983/from-a-to-z.svg"
                  class="sideMenuIcon"
                />Budget planner
              </div></a
            >
            <a href="achievements.php"
              ><div class="block sideMenuItem">
                <img
                  alt="side_menu_icon"
                  src="https://www.svgrepo.com/show/84275/trophy.svg"
                  class="sideMenuIcon"
                />Achievements
              </div></a
            >
          </div>
          <div style="align-self: flex-end; margin-bottom: 40px;">
            <a href="profile.php"
              ><div class="block sideMenuItem">
                <img
                  alt="side_menu_icon"
                  src="https://www.svgrepo.com/show/7025/user.svg"
                  class="sideMenuIcon"
                />Profile
              </div></a
            >
            <a href="settings.php"
              ><div class="block sideMenuItem">
                <img
                  alt="side_menu_icon"
                  src="https://www.svgrepo.com/show/198090/gear.svg"
                  class="sideMenuIcon"
                />Settings
              </div></a
            >
            <a href="faq.php"
              ><div class="block sideMenuItem">
                <img
                  alt="side_menu_icon"
                  src="https://www.svgrepo.com/show/348371/help.svg"
                  class="sideMenuIcon"
                />Help
              </div></a
            >
            <a href="index.php"
              ><div class="block sideMenuItem">
                <img
                  alt="side_menu_icon"
                  src="https://www.svgrepo.com/show/334066/log-out-circle.svg"
                  class="sideMenuIcon"
                />Log out
              </div>
            </a>
          </div>
        </div>
  
        <img
            id="menuClosed"
            alt="menuLogo"
            class="headerLogo"
            src="https://www.svgrepo.com/show/336031/hamburger-button.svg"
            style="position: fixed;"
        />
        
        <img 
            src="pictures/logo_header.png" 
            alt="logo_header" 
            class="finance-logo"
        />

        <a href="profile.php" style="margin-top: 2%; margin-right: 1%; position: absolute; right: 0">
        <img 
            src="<?php echo $image_src;  ?>"
            alt="profile_photo" 
            class="profile-logo"
        />
        </a>


    </header>

    <main style="max-width: calc(1100px + 550px + 50px); margin: auto;">
      <h1 style="text-align: center;">Budget</h1>
      <div class="block" style="width: 100%; flex-wrap: wrap; gap: 50px;">
        <div class="contentBox" style="max-width: 1100px; width: 100%; margin: 0;">
          <h2>Monthly budget</h2>
          <canvas id="largeCanvas"></canvas>
        </div>
        <div class="block" style="max-width: 550px; flex-wrap: wrap; gap: 50px;">
          <div class="contentBox" style="margin: 0; min-width: 100%;">
            <h2>Monthly income</h2>
            <canvas id="smallUpperCanvas"></canvas>
          </div>
          <div class="contentBox" style="margin: 0; min-width: 100%;">
            <h2>Card balances</h2>
            <canvas id="smallLowerCanvas"></canvas>
          </div>
        </div>

      </div>
    </main>

    <footer>
      <ul>
        <li>
          <a href="home.php">Home</a>
        </li>
        <li>
          <a href="faq.php">FAQ</a>
        </li>
        <li>
          <a href="about.php">About</a>
        </li>
        <li>
          <a href="contactForm/index.php">Contact</a>
        </li>
      </ul>
      <p>&copy; 2021 Finance Budget App AS</p>
    </footer>

    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script>

      const TYPE_BAR_HORISONTAL   = 0;
      const TYPE_BAR_VERTICAL     = 1;
      const TYPE_LINE             = 2;

      Chart.defaults.font.family  = "sans-serif";
      Chart.defaults.font.size    = 18;
      Chart.defaults.color        = "#262220";

      function createChart(canvas, names, data, type) {

        var chartType, axis, showLegend;

        switch(type) {

          case TYPE_BAR_HORISONTAL:
            chartType   = "bar";
            axis        = 'y';
            showLegend  = false;
            break;

          case TYPE_BAR_VERTICAL:
            chartType   = "bar";
            axis        = 'x';
            showLegend  = false;
            break;

          case TYPE_LINE:
            chartType   = "line";
            axis        = 'x';
            showLegend  = false;
            break;
        }

        var myData = {
          labels: names,
          datasets: [{
            data: data,
            backgroundColor: "#262220"
          }]
        };

        return new Chart(canvas, {
          type: chartType,
          data: myData,
          options: {
            indexAxis: axis,
            plugins: {
              legend: {
                display: showLegend
              }
            }
          }
        });
      }

      // Data vi kommer til å hente fra databasen
      
      var incomeValues  = [];
      var incomeNames   = [];

      var expenseValues = [];
      var expenseNames  = [];
      
      <?php 
        $sql    = "SELECT * FROM transactions WHERE budgetID='$budgetID';";
        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()) {

          if($row["transactionType"] == "income") {

            echo "incomeValues.push('" . $row['transactionValue'] . "');";
            echo "incomeNames.push('" . $row['transactionName'] . "');";
          } else {

            echo "expenseValues.push('" . $row['transactionValue'] . "');";
            echo "expenseNames.push('" . $row['transactionName'] . "');";
          }
        }
      ?>

      // Monthly budget
      var largeCanvas = document.getElementById("largeCanvas").getContext("2d");
      createChart(
        largeCanvas, 
        expenseNames, 
        expenseValues, 
        TYPE_BAR_HORISONTAL
      );

      // Monthly income
      var smallUpperCanvas = document.getElementById("smallUpperCanvas").getContext("2d");
      createChart(
        smallUpperCanvas, 
        incomeNames, 
        incomeValues, 
        TYPE_BAR_VERTICAL
      );
      // Card balances
      var smallLowerCanvas = document.getElementById("smallLowerCanvas").getContext("2d");
      createChart(
        smallLowerCanvas, 
        ["10.05", "11.05", "12.05", "13.05", "14.05", "15.05", "16.05", "17.05", "18.05", "19.05", "20.05", "21.05"], 
        [100000, 97500, 96403, 85931, 76301, 156043, 156043, 155371, 150379, 140579, 130271, 60000], 
        TYPE_LINE
      );


    </script>
  </body>
</html>
