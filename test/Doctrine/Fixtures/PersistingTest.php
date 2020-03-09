<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FieldDef;
use Doctrine\ORM\Mapping;
use FactoryGirl\Provider\Doctrine\FixtureFactory;

class PersistingTest extends TestCase
{
    /**
     * @test
     */
    public function automaticPersistCanBeTurnedOn()
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, ['name' => 'Zeta']);

        $fixtureFactory->persistOnGet();

        $ss = $fixtureFactory->get(Entity\SpaceShip::class);
        $entityManager->flush();

        $this->assertNotNull($ss->getId());
        $this->assertSame($ss, $entityManager->find(Entity\SpaceShip::class, $ss->getId()));
    }

    /**
     * @test
     */
    public function doesNotPersistByDefault()
    {
        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Entity\SpaceShip::class, ['name' => 'Zeta']);

        $ss = $fixtureFactory->get(Entity\SpaceShip::class);

        $entityManager->flush();

        $this->assertNull($ss->getId());
        $q = $entityManager
            ->createQueryBuilder()
            ->select('ss')
            ->from(Entity\SpaceShip::class, 'ss')
            ->getQuery();
        $this->assertEmpty($q->getResult());
    }

    /**
     * @test
     */
    public function doesNotPersistEmbeddableWhenAutomaticPersistingIsTurnedOn()
    {
        $mappingClasses = [
            Mapping\Embeddable::class,
            Mapping\Embedded::class,
        ];

        foreach ($mappingClasses as $mappingClass) {
            if (!class_exists($mappingClass)) {
                $this->markTestSkipped('Doctrine Embeddable feature not available');
            }
        }

        $entityManager = self::createEntityManager();

        $fixtureFactory = new FixtureFactory($entityManager);

        $fixtureFactory->defineEntity(Entity\Name::class, [
            'first' => FieldDef::sequence(static function () {
                $values = [
                    null,
                    'Doe',
                    'Smith',
                ];

                return $values[array_rand($values)];
            }),
            'last' => FieldDef::sequence(static function () {
                $values = [
                    null,
                    'Jane',
                    'John',
                ];

                return $values[array_rand($values)];
            }),
        ]);

        $fixtureFactory->defineEntity(Entity\Commander::class, [
            'name' => FieldDef::reference(Entity\Name::class),
        ]);

        $fixtureFactory->persistOnGet();

        /** @var Entity\Commander $commander */
        $commander = $fixtureFactory->get(Entity\Commander::class);

        $this->assertInstanceOf(Entity\Commander::class, $commander);
        $this->assertInstanceOf(Entity\Name::class, $commander->name());

        $entityManager->flush();
    }
}
