
<?php
                $query = "SELECT * FROM appointments WHERE status='Pending'";
                $result = mysqli_query($conn, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="appointment-item">';
                    echo '<div class="appointment-details">';
                    echo '<img class="client-avatar" src="https://placehold.co/48x48" alt="Client avatar" />';
                    echo '<div>';
                    echo '<div class="client-name">' . $row['clientname'] . '</div>';
                    echo '<div class="appointment-date">' . $row['recieve_at'] . '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="appointment-actions">';
                    echo '<form method="post" style="display:inline;">';
                    echo '<input type="hidden" name="appointment_id" value="' . $row['id'] . '">';
                    echo '<button type="submit" name="accept" class="accept-button">Accept</button>';
                    echo '</form>';
                    echo '<button class="decline-button">Decline</button>';
                    echo '<button class="details-button">➔</button>';
                    echo '</div>';
                    echo '</div>';
                }
                
                ?>
