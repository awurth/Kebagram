<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{

    protected $table = 'hashtag';

    protected $primaryKey = 'id';

    public static function saveHashtags(Picture $picture, array $tags)
    {
        // Parse tags in caption saved in database
        $oldCaptionTags = array();
        preg_match_all('/#(\w+)/', $picture->description, $oldCaptionTags);

        // Difference between old and new caption
        $removedTags = array_diff($oldCaptionTags[1], $tags);
        $addedTags = array_diff($tags, $oldCaptionTags[1]);

        $detach = array();
        $increments = array();
        $decrements = array();

        $existingTags = $picture->hashtags;
        foreach ($existingTags as $tag) {
            // If the tags saved in database contain a tag that is not in the new caption
            // and it is used only once
            if (in_array($tag->name, $removedTags)) {
                if ($tag->pivot->count <= 1) {
                    $detach[] = $tag->id;
                } else {
                    $decrements[] = [
                        'id' => $tag->id,
                        'count' => $tag->pivot->count
                    ];
                }
            }

            if (in_array($tag->name, $addedTags)) {
                $increments[] = [
                    'id' => $tag->id,
                    'count' => $tag->pivot->count
                ];
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

            $picture->hashtags()->attach($hashtag->id, ['count' => 1]);
        }

        // Detach removed tags from the picture
        if (!empty($detach)) {
            $picture->hashtags()->detach($detach);
        }

        foreach ($increments as $increment) {
            $picture->hashtags()->updateExistingPivot($increment['id'], [
                'count' => $increment['count'] + 1
            ]);
        }

        foreach ($decrements as $decrement) {
            $picture->hashtags()->updateExistingPivot($decrement['id'], [
                'count' => $decrement['count'] + 1
            ]);
        }
    }

    public function pictures()
    {
        return $this->belongsToMany('App\Models\Picture')->withPivot('count');
    }
}
