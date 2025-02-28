
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $amount = (int) $_POST['amount']; // Cast amount to integer
        $description = $_POST['description'];
        $remarks = $_POST['remarks'];

        require_once('../vendor/autoload.php');

        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('POST', 'https://api.paymongo.com/v1/links', [
                'body' => json_encode([
                    'data' => [
                        'attributes' => [
                            'amount' => $amount,
                            'description' => $description,
                            'remarks' => $remarks
                        ]
                    ]
                ]),
                'headers' => [
                    'accept' => 'application/json',
                    'authorization' => 'Basic c2tfdGVzdF9zTWNlcjJNZ3ZvY3RFM1VCb0NVcmdGa3g6',
                    'content-type' => 'application/json',
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $checkoutUrl = $responseBody['data']['attributes']['checkout_url'];

            echo "<script>window.open('$checkoutUrl', '_blank');</script>";
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getResponse()->getBody()->getContents()) . "</p>";
        }
    }
    ?>
