<?php

namespace AtlanteGroup\CorporateVCardsBundle\Service;

use AtlanteGroup\CorporateVCardsBundle\Helper\Util;
use JeroenDesloovere\VCard\VCard;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;

class VCardService
{
    /** @var array */
    private $profiles;

    /** @var array */
    private $defaultProfile;

    /** @var AssetsHelper */
    private $assets;

    public function __construct($profiles, $defaultProfile, $assets)
    {
        $this->profiles = $profiles;
        $this->defaultProfile = $defaultProfile;
        $this->assets = $assets;
    }

    /**
     * Get a profile array for a person
     * @param $person
     * @return array|null
     */
    public function getProfile($person)
    {
        if (!array_key_exists($person, $this->profiles))
            return null;

        // Merge profile with default values
        return Util::array_merge_recursive_distinct($this->defaultProfile, $this->profiles[$person]);
    }

    /**
     * Returns all profiles as an associative array
     * @return array
     */
    public function getProfiles()
    {
        $profiles = [];

        foreach (array_keys($this->profiles) as $person)
            $profiles[$person] = $this->getProfile($person);

        return $profiles;
    }

    /**
     * Returns a vcard for a profile
     * @param $profile
     * @param $includePhoto
     * @return VCard
     */
    public function getVCard($profile, $includePhoto)
    {
        $vcard = new VCard();
        $vcard
            ->addName($profile['lastName'], $profile['firstName'])
            ->addCompany($profile['company'])
            ->addAddress(
                '',
                '',
                $profile['address']['street'],
                $profile['address']['city'],
                $profile['address']['region'],
                $profile['address']['zip'],
                $profile['address']['country']
            )
            ->addEmail($profile['email'])
            ->addURL($profile['url'])
            ->addPhoneNumber($profile['phone']['work'], 'WORK')
            ->addPhoneNumber($profile['phone']['mobile'], 'CELL')
            ->addJobtitle($profile['jobTitle']);

        // Add photo
        $vcard->addPhoto($this->assets->getUrl($profile['photo']), $includePhoto);

        return $vcard;
    }
}