<?php
namespace FactoryGirl\Tests\Provider\Doctrine\Fixtures;

use Ergebnis\FactoryBot\Test\Fixture\Entity;
use FactoryGirl\Provider\Doctrine\FieldDef;
use Doctrine\ORM\Mapping;

class PersistingTest extends TestCase
{
    /**
     * @test
     */
    public function automaticPersistCanBeTurnedOn()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class, ['name' => 'Zeta']);

        $this->factory->persistOnGet();
        $ss = $this->factory->get(Entity\SpaceShip::class);
        $this->em->flush();

        $this->assertNotNull($ss->getId());
        $this->assertSame($ss, $this->em->find(Entity\SpaceShip::class, $ss->getId()));
    }

    /**
     * @test
     */
    public function doesNotPersistByDefault()
    {
        $this->factory->defineEntity(Entity\SpaceShip::class, ['name' => 'Zeta']);
        $ss = $this->factory->get(Entity\SpaceShip::class);
        $this->em->flush();

        $this->assertNull($ss->getId());
        $q = $this->em
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

        $this->factory->defineEntity(Entity\Name::class, [
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

        $this->factory->defineEntity(Entity\Commander::class, [
            'name' => FieldDef::reference(Entity\Name::class),
        ]);

        $this->factory->persistOnGet();

        /** @var Entity\Commander $commander */
        $commander = $this->factory->get(Entity\Commander::class);

        $this->assertInstanceOf(Entity\Commander::class, $commander);
        $this->assertInstanceOf(Entity\Name::class, $commander->name());

        $this->em->flush();
    }
}
