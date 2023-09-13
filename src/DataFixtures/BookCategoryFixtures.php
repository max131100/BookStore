<?php

namespace App\DataFixtures;

use App\Entity\BookCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            (new BookCategory())->setTitle('Devices')->setSlug('devices'),
            (new BookCategory())->setTitle('Android')->setSlug('android'),
            (new BookCategory())->setTitle('Data')->setSlug('data'),
            (new BookCategory())->setTitle('Operations & Cloud')->setSlug('operations-and-cloud'),
            (new BookCategory())->setTitle('Python')->setSlug('python'),
            (new BookCategory())->setTitle('Databases')->setSlug('databases'),
            (new BookCategory())->setTitle('Testing')->setSlug('testing'),
            (new BookCategory())->setTitle('Quantum computing')->setSlug('quantum-computing'),
            (new BookCategory())->setTitle('Machine learning')->setSlug('machine-learning'),
            (new BookCategory())->setTitle('Operation systems')->setSlug('operation-systems'),
            (new BookCategory())->setTitle('Version Control Systems')->setSlug('version-control-systems'),
            (new BookCategory())->setTitle('Computer Vision')->setSlug('computer-vision'),
            (new BookCategory())->setTitle('Web Design')->setSlug('web-design'),
            (new BookCategory())->setTitle('Amazon Web Services')->setSlug('amazon-web-services'),
            (new BookCategory())->setTitle('Algorithms')->setSlug('algorithms'),
            (new BookCategory())->setTitle('Linear Algebra')->setSlug('linear-algebra'),
        ];

        foreach ($categories as $category) {
            $manager->persist($category);
        }

        $manager->persist((new BookCategory())->setTitle('Networking')->setSlug('networking'));

        $manager->flush();
    }
}
