<?php
    # include the API
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $Documents = new PerchMembers_Documents($API);

    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);

    header('Content-Type: application/json');

    $response = [
        'success' => false,
        'message' => 'Invalid input.',
    ];

    http_response_code(400);

    if (is_array($data)) {
        $action = isset($data['action']) ? (string) $data['action'] : 'update-status';

        switch ($action) {
            case 'delete-document':
                $documentId = null;
                if (isset($data['documentId'])) {
                    $documentId = (int) $data['documentId'];
                } elseif (isset($data['selectId'])) {
                    $documentId = (int) $data['selectId'];
                }

                if ($documentId && $documentId > 0) {
                    $result = $Documents->delete_document($documentId);
                    $response = array_merge([
                        'action' => 'delete-document',
                        'documentId' => $documentId,
                    ], $result);

                    if (!empty($result['success'])) {
                        http_response_code(200);
                    }
                } else {
                    $response['message'] = 'A valid document ID is required.';
                }
                break;

            case 'update-status':
            case '':
                $documentId = null;
                if (isset($data['documentId'])) {
                    $documentId = (int) $data['documentId'];
                } elseif (isset($data['selectId'])) {
                    $documentId = (int) $data['selectId'];
                }

                $status = null;
                if (isset($data['status'])) {
                    $status = (string) $data['status'];
                } elseif (isset($data['selectedValue'])) {
                    $status = (string) $data['selectedValue'];
                }

                $allowed_statuses = ['pending', 'accepted', 'declined', 'rerequest'];

                if (!$documentId || $documentId <= 0) {
                    $response['message'] = 'A valid document ID is required.';
                    break;
                }

                if (!in_array($status, $allowed_statuses, true)) {
                    $response['message'] = 'A valid document status is required.';
                    break;
                }

                $updated = $Documents->update_document_status($documentId, $status);

                if ($updated) {
                    $response = [
                        'success' => true,
                        'message' => 'Document status updated.',
                        'action' => 'update-status',
                        'documentId' => $documentId,
                        'status' => $status,
                    ];
                    http_response_code(200);
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Unable to update the document status.',
                        'action' => 'update-status',
                        'documentId' => $documentId,
                        'status' => $status,
                    ];
                }
                break;

            default:
                $response['message'] = 'Unsupported action.';
                break;
        }
    }

    echo json_encode($response);
?>
