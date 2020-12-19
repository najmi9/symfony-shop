 /**
     * dataProvider provideUrls
     */
    public function PageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function provideUrls()
    {
        return [
            ['/'],
            ['/blog'],
            ['/contact'],
            // ...
        ];
    }