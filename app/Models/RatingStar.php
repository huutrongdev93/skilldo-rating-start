<?php
namespace RatingStar\Models;

use SkillDo\Cms\Support\Metadata;
use Illuminate\Support\Facades\DB;
use SkillDo\Database\Eloquent\Model;
/**
 * @property string object_type
 */
class RatingStar extends Model
{
    protected string $table = 'rating_star';

    protected array $columns = [
        'name'          => ['string'],
        'email'         => ['string'],
        'title'         => ['string'],
        'message'       => ['string'],
        'star'          => ['int', 0],
        'object_type'   => ['string', 'products'],
        'object_id'     => ['int', 0],
        'is_read'       => ['int', 0],
        'parent_id'     => ['int', 0],
        'status'        => ['string', 'public'],
        'type'          => ['string', 'handmade'],
        'user_id'       => ['int', 0],
        'like'          => ['int', 0],
    ];

    const USER_CREATED_AT = 'user_id';

    protected static function boot(): void
    {
        parent::boot();

        static::deleted(function (RatingStar $wheel, $listRemoveId, $objects) {

            foreach ($objects as $object) {

                if($object->object_type != 'comment') {

                    $count_rating_star = Metadata::get($object->object_type, $object->object_id, $wheel->getTable(), true);

                    if (!hasItems($count_rating_star))
                    {
                        $count_rating_star = array('count' => 0, 'star' => 0);
                    }
                    else
                    {
                        $count_rating_star['count'] = $count_rating_star['count'] - 1;
                        $count_rating_star['star'] = $count_rating_star['star'] - $object->star;
                    }

                    if ($object->status == 'public') {
                        Metadata::update($object->object_type, $object->object_id, $wheel->getTable(), $count_rating_star);
                    }

                    DB::table($wheel->getTable())->where('object_type', 'comment')->where('parent_id', $object->id)->delete();
                }
            }
        });
    }
}