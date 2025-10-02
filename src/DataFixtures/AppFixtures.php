<?php

namespace App\DataFixtures;

use App\Entity\Advice;
use App\Entity\User;
use App\Enum\MonthEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Users

        $user = $this->userMaker([
            'email' => 'user@ecogarden.com',
            'password' => 'password',
            'zip_code' => 59400,
        ]);
        $manager->persist($user);

        $admin = $this->userMaker([
            'email' => 'admin@ecogarden.com',
            'password' => 'password',
            'zip_code' => 25220,
            'roles' => ['ROLE_ADMIN'],
        ]);
        $manager->persist($admin);

        // Advices

        $advice1 = $this->adviceMaker([
            'content' => 'In January, it\'s a good time to plan your garden for the upcoming year. Consider what vegetables and flowers you want to plant and order seeds early.',
            'months' => [MonthEnum::JANUARY],
        ]);
        $manager->persist($advice1);

        $advice2 = $this->adviceMaker([
            'content' => 'February is the perfect month to start sowing seeds indoors for vegetables like tomatoes, peppers, and eggplants. Make sure to provide adequate light and warmth for germination.',
            'months' => [MonthEnum::FEBRUARY],
        ]);
        $manager->persist($advice2);

        $advice3 = $this->adviceMaker([
            'content' => 'March is the time to prepare your garden beds. Clear out any debris from winter, add compost or organic matter to enrich the soil, and start planting cool-season crops like lettuce, spinach, and peas.',
            'months' => [MonthEnum::MARCH],
        ]);
        $manager->persist($advice3);

        $advice4 = $this->adviceMaker([
            'content' => 'In April, you can start planting hardy vegetables like carrots, radishes, and broccoli directly in the garden. It\'s also a good time to plant fruit trees and berry bushes.',
            'months' => [MonthEnum::APRIL],
        ]);
        $manager->persist($advice4);

        $advice5 = $this->adviceMaker([
            'content' => 'May is the month to plant warm-season crops like tomatoes, cucumbers, and beans. Make sure to harden off seedlings before transplanting them outdoors to prevent shock.',
            'months' => [MonthEnum::MAY],
        ]);
        $manager->persist($advice5);

        $advice6 = $this->adviceMaker([
            'content' => 'In June, focus on maintaining your garden by watering regularly, mulching to retain moisture, and keeping an eye out for pests and diseases. It\'s also a great time to start harvesting early crops like lettuce and radishes.',
            'months' => [MonthEnum::JUNE],
        ]);
        $manager->persist($advice6);

        $advice7 = $this->adviceMaker([
            'content' => 'July is the peak of summer, so make sure to water your garden deeply and consistently. Harvest crops like beans, cucumbers, and zucchini regularly to encourage continued production.',
            'months' => [MonthEnum::JULY],
        ]);
        $manager->persist($advice7);

        $advice8 = $this->adviceMaker([
            'content' => 'In August, continue to harvest your crops and consider planting a second round of fast-growing vegetables like lettuce and radishes for a fall harvest. Keep an eye on watering needs as temperatures remain high.',
            'months' => [MonthEnum::AUGUST],
        ]);
        $manager->persist($advice8);

        $advice9 = $this->adviceMaker([
            'content' => 'September is the time to start preparing your garden for fall. Plant cool-season crops like kale, Brussels sprouts, and carrots. It\'s also a good time to start cleaning up spent plants and adding compost to the soil.',
            'months' => [MonthEnum::SEPTEMBER],
        ]);
        $manager->persist($advice9);

        $advice10 = $this->adviceMaker([
            'content' => 'In October, focus on harvesting the last of your summer crops and planting garlic for a summer harvest next year. It\'s also a good time to mulch garden beds to protect them over the winter.',
            'months' => [MonthEnum::OCTOBER],
        ]);
        $manager->persist($advice10);

        $advice11 = $this->adviceMaker([
            'content' => 'November is the month to finish cleaning up your garden. Remove any remaining plant debris to prevent pests and diseases from overwintering. Consider planting cover crops to improve soil health.',
            'months' => [MonthEnum::NOVEMBER],
        ]);
        $manager->persist($advice11);

        $advice12 = $this->adviceMaker([
            'content' => 'In December, take time to reflect on the past gardening season and plan for the next year. Order seeds, research new gardening techniques, and consider any changes you want to make to your garden layout.',
            'months' => [MonthEnum::DECEMBER],
        ]);
        $manager->persist($advice12);

        $advice13 = $this->adviceMaker([
            'content' => 'Spring is a great time to start a compost pile. Collect kitchen scraps like vegetable peels, coffee grounds, and eggshells, along with yard waste like grass clippings and leaves. Turn the pile regularly to speed up decomposition and create nutrient-rich compost for your garden.',
            'months' => [MonthEnum::MARCH, MonthEnum::APRIL, MonthEnum::MAY],
        ]);
        $manager->persist($advice13);

        $advice14 = $this->adviceMaker([
            'content' => 'Summer is the perfect time to practice crop rotation in your garden. By changing the location of your crops each year, you can prevent soil depletion and reduce the risk of pests and diseases. Plan your garden layout to ensure that no plant family is grown in the same spot for at least three years.',
            'months' => [MonthEnum::JUNE, MonthEnum::JULY, MonthEnum::AUGUST],
        ]);
        $manager->persist($advice14);

        $manager->flush();
    }

    private function userMaker(array $params): User
    {
        $user = new User();
        $user->setEmail($params['email']);
        $user->setPassword($params['password']);
        $user->setZipCode($params['zip_code']);
        if (isset($params['roles'])) {
            $user->setRoles($params['roles']);
        }

        return $user;
    }

    private function adviceMaker(array $params): Advice
    {
        $advice = new Advice();
        $advice->setContent($params['content']);
        $advice->setMonths($params['months']);

        return $advice;
    }
}
