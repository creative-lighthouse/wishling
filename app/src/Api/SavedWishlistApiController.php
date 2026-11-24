<?php

namespace App\Api;

use App\Model\Wishlist;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\ORM\FieldType\DBDatetime;

/**
 * /api/saved – gemerkte fremde Wunschlisten, nur für eingeloggte User
 *
 *   GET    /        gemerkte Listen (neueste zuerst)
 *   PUT    /$Hash   Liste merken
 *   DELETE /$Hash   Liste nicht mehr merken
 *
 * Archivierte Listen bleiben gemerkt, werden aber nicht angezeigt,
 * da Gäste sie ohnehin nicht öffnen können.
 */
class SavedWishlistApiController extends ApiController
{
    private static $url_handlers = [
        'PUT $Hash!' => 'saveWishlist',
        'DELETE $Hash!' => 'unsaveWishlist',
        'GET ' => 'listSaved',
    ];

    private static $allowed_actions = [
        'listSaved',
        'saveWishlist',
        'unsaveWishlist',
    ];

    public function listSaved(): HTTPResponse
    {
        $user = $this->requireUser();

        $lists = [];
        foreach ($user->SavedWishlists()->filter('Archived', false)->sort('SavedAt', 'DESC') as $wishlist) {
            $lists[] = $wishlist->toApiSavedSummary();
        }

        return $this->json(['wishlists' => $lists]);
    }

    public function saveWishlist(): HTTPResponse
    {
        $user = $this->requireUser();
        $wishlist = $this->wishlistByHash(true);

        if ($wishlist->isOwnedBy($user)) {
            $this->fail('Das ist deine eigene Wunschliste.', 403);
        }

        if (!$user->hasSaved($wishlist)) {
            $user->SavedWishlists()->add($wishlist, ['SavedAt' => DBDatetime::now()->Rfc2822()]);
        }

        return $this->json(['saved' => true, 'savedCount' => $user->savedCount()]);
    }

    public function unsaveWishlist(): HTTPResponse
    {
        $user = $this->requireUser();
        // Auch archivierte Listen dürfen entfernt werden
        $wishlist = $this->wishlistByHash(false);

        $user->SavedWishlists()->remove($wishlist);

        return $this->json(['saved' => false, 'savedCount' => $user->savedCount()]);
    }

    private function wishlistByHash(bool $activeOnly): Wishlist
    {
        $hash = (string) $this->getRequest()->param('Hash');
        $filter = ['Hash' => $hash];
        if ($activeOnly) {
            $filter['Archived'] = false;
        }

        /** @var Wishlist|null $wishlist */
        $wishlist = $hash !== '' ? Wishlist::get()->filter($filter)->first() : null;
        if (!$wishlist) {
            $this->fail('Wunschliste nicht gefunden.', 404);
        }
        return $wishlist;
    }
}
