    <footer style="width: 100%; height: 60px; background-color: DarkSlateBlue;" class="mt-4 fixed-bottom">
      <div class="container">
        <p style="text-align: center; padding: 10px; color: white;">&copy; Algonquin College 2010 - 2023. All Rights Reserved</p>
      </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.6/dist/js/bootstrap.min.js"></script>
    <script>
      function confirmDeletion(e) {
        if(!confirm("The selected friends will be defriended")) {
            e.preventDefault();
        }
      }

      // function confirmDeny(e) {
      //   if(!confirm("The selected requests will be denied")) {
      //       e.preventDefault();
      //   }
      // }
      function confirmDeny(event) {
        const confirmed = confirm("Are you sure you want to deny the selected friend requests?");
        if (!confirmed) {
            event.preventDefault(); // Stop form submission if not confirmed
            return false;
        }
        return true; // Allow form submission if confirmed
    }

    // Bypass onsubmit for "Accept Selected"
    function submitWithoutConfirm(button) {
        const form = button.closest('form'); // Get the closest form
        const submitButton = document.getElementById('acceptRequest'); // Create a hidden submit button
        form.submit();
    }
    </script>
</body>
</html> 