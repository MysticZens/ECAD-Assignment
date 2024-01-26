<?php
session_start();
include("header.php"); // Include the Page Layout header
include_once("mysql_conn.php");
?>

<div class="table-responsive" style="width: 80%; margin: auto">
	<div style="margin-bottom:20px;">
		<a href="createFeedback.php"><button class="submitbutton">Review</button></a>
	</div>
    <table class="table table-striped table-bordered" style="white-space:nowrap;">
    <thead class="table" style="text-align: center;">
		<tr>
			<th style="background-color: red; color: white;">Shopper's Name</th>
			<th style="background-color: red; color: white;">Subject</th>
			<th style="background-color: red; color: white;">Content</th>
			<th style="background-color: red; color: white;">Rating</th>
			<th style="background-color: red; color: white;">Date Created</th>
		</tr>
	</thead>
	<tbody class="table">
		<!-- Table Body -->
		<?php
			$qry = "SELECT s.Name, f.* FROM feedback f 
					INNER JOIN shopper s 
					ON s.ShopperID = f.ShopperID";
			$result = $conn->query($qry);
  			if ($result->num_rows > 0) {
				// Output data of each row
				while($row = $result->fetch_array()) {
					echo "<tr>";
					echo "<td style='background-color: #e66d5d'>" . htmlspecialchars($row["Name"]) . "</td>";
					echo "<td style='background-color: #e66d5d'>" . htmlspecialchars($row["Subject"]) . "</td>";
					echo "<td style='background-color: #e66d5d'>" . htmlspecialchars($row["Content"]) . "</td>";
					echo "<td style='background-color: #e66d5d; text-align: center;'>";
					for ($i=0; $i<$row["Rank"]; $i++) {
						echo "<i class='fa-solid fa-star' style='color: #ffe63b; background-color: #e66d5d; margin: 0; padding: 0'></i>";
					}
					echo "</td>";
					// Add more cells as needed
					echo "<td style='background-color: #e66d5d'>" . htmlspecialchars($row["DateTimeCreated"]) . "</td>";
					echo "</tr>";
				}
			} 
			else {
				echo "<tr><td colspan='4'><p style='color: red; font-size: 25px; text-align: center'><b>No records found!</b><p></td></tr>";
			}
  		?>
    </tbody>
    </table>
</div>
<br />
<?php 
$conn->close();
include("footer.php"); // Include the Page Layout footer
?>
