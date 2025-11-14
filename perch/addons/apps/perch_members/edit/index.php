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

echo <<<'JS'
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const setStatusMessage = function (elementId, message, isError) {
      const element = document.getElementById(elementId);
      if (!element) return;

      element.textContent = message;

      if (isError) {
        element.classList.add('error');
      } else {
        element.classList.remove('error');
      }

      if (message) {
        window.setTimeout(function () {
          element.textContent = '';
          element.classList.remove('error');
        }, 4000);
      }
    };

    const sendRequest = function (payload) {
      return fetch('handler.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify(payload)
      }).then(function (response) {
        return response.json().catch(function () {
          return {};
        }).then(function (data) {
          if (!response.ok || !data.success) {
            const error = new Error(data.message || 'Request failed.');
            error.data = data;
            error.status = response.status;
            throw error;
          }
          return data;
        });
      });
    };

    document.querySelectorAll('select[name="docstatus"]').forEach(function (select) {
      select.addEventListener('change', function () {
        const documentId = this.dataset.documentId;
        const status = this.value;

        if (!documentId) {
          return;
        }

        sendRequest({
          action: 'update-status',
          documentId: parseInt(documentId, 10),
          status: status
        })
          .then(function (data) {
            const message = data.message || 'Saved';
            setStatusMessage('document-result-' + documentId, message, false);
          })
          .catch(function (error) {
            console.error('Error updating document status:', error);
            const message = error.data && error.data.message ? error.data.message : 'Unable to update document status.';
            setStatusMessage('document-result-' + documentId, message, true);
          });
      });
    });

    const reminderHiddenField = document.getElementById('document-reminder-status-input');
    if (reminderHiddenField) {
      const reminderCheckboxes = Array.prototype.slice.call(document.querySelectorAll('.document-reminder-checkbox'));
      const defaultValue = 'all_approved';

      const syncCheckboxes = function (targetValue) {
        reminderCheckboxes.forEach(function (checkbox) {
          const optionValue = checkbox.dataset.value || '';
          checkbox.checked = optionValue === targetValue;
        });
      };

      const ensureSelection = function () {
        const currentValue = reminderHiddenField.value || defaultValue;
        syncCheckboxes(currentValue);
        reminderHiddenField.value = currentValue;
      };

      ensureSelection();

      reminderCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
          const optionValue = this.dataset.value || '';

          if (this.checked) {
            reminderHiddenField.value = optionValue || defaultValue;
            reminderCheckboxes.forEach(function (other) {
              if (other !== checkbox) {
                other.checked = false;
              }
            });
          } else {
            if (reminderHiddenField.value === optionValue) {
              reminderHiddenField.value = defaultValue;
            }
            window.setTimeout(ensureSelection, 0);
          }
        });
      });
    }

    document.querySelectorAll('.action-delete-document').forEach(function (button) {
      button.addEventListener('click', function () {
        const documentId = this.dataset.documentId;
        const documentName = this.dataset.documentName || '';

        if (!documentId) {
          return;
        }

        const confirmMessage = documentName
          ? 'Are you sure you want to delete "' + documentName + '"?'
          : 'Are you sure you want to delete this document?';

        if (!window.confirm(confirmMessage)) {
          return;
        }

        sendRequest({
          action: 'delete-document',
          documentId: parseInt(documentId, 10)
        })
          .then(function (data) {
            if (data.message && data.message !== 'Document deleted.') {
              window.alert(data.message);
            }

            const row = document.querySelector('tr[data-document-id="' + documentId + '"]');
            if (row) {
              row.remove();
            }
          })
          .catch(function (error) {
            console.error('Error deleting document:', error);
            const message = error.data && error.data.message ? error.data.message : 'Unable to delete document.';
            setStatusMessage('document-delete-result-' + documentId, message, true);
          });
      });
    });
  });
</script>
JS;

    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
?>
