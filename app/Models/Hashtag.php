<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $table = 'hashtag';

    protected $primaryKey = 'id';

    public static function saveHashtags(Picture $picture, array $addedTags, array $removedTags)
    {
        $detach = array();

        $existingTags = $picture->hashtags;
        foreach ($existingTags as $tag) {
            // If the tags saved in database contain a tag that is not in the new caption
            // and it is used only once
            if (in_array($tag->name, $removedTags)) {
                if ($tag->pivot->count <= 1) {
                    $detach[] = $tag->id;
                } else {
                    $picture->hashtags()->updateExistingPivot($tag->id, [
                        'count' => $tag->pivot->count - 1
                    ]);
                }
            }

            if (in_array($tag->name, $addedTags)) {
                $picture->hashtags()->updateExistingPivot($tag->id, [
                    'count' => $tag->pivot->count + 1
                ]);
            }
        }

        // Add the new tags in database and attach them to the picture
        foreach ($addedTags as $tag) {
            $hashtag = Hashtag::where('name', $tag)->first();
            if (!$hashtag) {
                $hashtag = new Hashtag();
                $hashtag->name = $tag;
                $hashtag->save();
            }

            if (!$existingTags->contains('name', $tag)) {
                $picture->hashtags()->attach($hashtag->id, ['count' => 1]);
            }
        }

        // Detach removed tags from the picture
        if (!empty($detach)) {
            $picture->hashtags()->detach($detach);
        }
    }

    public static function parseHashtags($text)
    {
        $tags = array();
        preg_match_all('/#(\w+)/', $text, $tags);

        return $tags[1];
    }

    public function pictures()
    {
        return $this->belongsToMany('App\Models\Picture')->withPivot('count');
    }
}
