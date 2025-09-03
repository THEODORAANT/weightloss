<?php
    # include the API
    include('../../../../core/inc/api.php');
    
    $API  = new PerchAPI(1.0, 'perch_members');
    $HTML   = $API->get('HTML');
    $Lang   = $API->get('Lang');
    $Paging = $API->get('Paging');

    # Set the page title
    $Perch->page_title = $Lang->get('Edit Members');

    # Do anything you want to do before output is started
    include('../modes/_subnav.php');
    include('../modes/members.edit.pre.php');
    
    
    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    
    # Display your page
    include('../modes/members.edit.post.php');
echo "<script>
        document.querySelectorAll('select[name=docstatus]').forEach(select => {
          select.addEventListener('change', function () {
            const dataToSend = {
              selectId: this.id,
              selectedValue: this.value
            };

            fetch('handler.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
              },
              body: JSON.stringify(dataToSend)
            })
            .then(response => response.text())
            .then(data => {
              console.log('Server response:', data);
              const resultDiv = document.getElementById('result-' + dataToSend.selectId);
              if (resultDiv) {
                resultDiv.textContent = 'saved!';
              }
            })
            .catch(error => {
              console.error('Error:', error);
            });
          });
        });
      </script>";
    
    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
?>
