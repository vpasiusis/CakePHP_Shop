<?php


use Phinx\Seed\AbstractSeed;

class ProductRatingsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
            'product_id' => '1',
            'score' => 2,
            'created' => date('Y-m-d H:i:s'),
            ],
            [
                'product_id' => '2',
                'score' => 4,
                'created' => date('Y-m-d H:i:s'),
            ],
            [
                'product_id' => '3',
                'score' => 5,
                'created' => date('Y-m-d H:i:s'),
            ]
            ];
            $posts = $this->table('product_ratings');
            $posts->insert($data)
            ->save();
    }
}
