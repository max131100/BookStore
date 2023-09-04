<?php

namespace App\Tests;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Entity\Review;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class MockUtils
{
    public static function createUser(): User {
        return (new User())
            ->setEmail('max@max.com')
            ->setFirstName('Max')
            ->setLastName('Kyrychenko')
            ->setRoles(['ROLE_AUTHOR'])
            ->setPassword('password');

    }

    public static function createBookCategory(): BookCategory
    {
        return (new BookCategory())->setTitle('Devices')->setSlug('devices');
    }

    public static function createBookFormat(): BookFormat
    {
        return (new BookFormat())
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null);
    }

    public static function createBookFormatLink(Book $book, BookFormat $format): BookToBookFormat
    {
        return (new BookToBookFormat())
            ->setBook($book)
            ->setFormat($format)
            ->setPrice(123.55)
            ->setDiscountPercent(5);
    }

    public static function createBook(): Book
    {
        return (new Book())
            ->setTitle('Test book')
            ->setImage('http://localhost.png')
            ->setIsbn('123321')
            ->setDescription('test')
            ->setPublicationDate(new DateTimeImmutable('2020-10-10'))
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection([]))
            ->setSlug('test-book')
            ->setMeap(false);
    }

    public static function createReview(Book $book): Review
    {
        return (new Review())
            ->setAuthor('tester')
            ->setContent('test content')
            ->setCreatedAt(new DateTimeImmutable())
            ->setRating(5)
            ->setBook($book);
    }
}
