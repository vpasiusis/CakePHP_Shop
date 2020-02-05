<?php


use Phinx\Seed\AbstractSeed;

class ProductSeed extends AbstractSeed
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
        $this->execute('TRUNCATE TABLE products');
        $data = [
            [
            'name' => 'Tomato',
            'price' => 2.4,
            'description' => 'Very fresh vegetable',
            'photo' => 'Tomato01.jpg',
            'modified' => date('Y-m-d H:i:s'),
            'created' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Cabbage',
                'price' => 1.8,
                'description' => 'A vegetable which is healthy',
                'photo' => 'Cabbage01.jpg',
                'modified' => date('Y-m-d H:i:s'),
                'created' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Almond Peanuts',
                'price' => 16.9,
                'description' => 'Perfect snack',
                'photo' => 'AlmondPeanuts01.jpg',
                'modified' => date('Y-m-d H:i:s'),
                'created' => date('Y-m-d H:i:s'),
            ]
            ];
            $posts = $this->table('products');
            $posts->insert($data)
            ->save();
            
    }
}
