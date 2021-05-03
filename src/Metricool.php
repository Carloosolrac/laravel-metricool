<?php

namespace Carloosolrac\Metricool;

use Illuminate\Support\Facades\Http;

class Metricool
{
    private $token;
    private $user;
    private $base;
    private $url;
    private $company;
    private $fields = [];

    public function __construct()
    {
        $this->token = config('laravel-metricool.token');
        $this->user = config('laravel-metricool.userId');
        $this->base = config('laravel-metricool.baseUrl');
    }

    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    public function companies()
    {
        return Http::get($this->getUrl('api/admin/profiles'))->json();
    }

    public function editDraft($post, $draft)
    {
        $this->fields = [];
        $this->addField('userId', $this->user);
        $this->addField('userToken', $this->token);
        $this->addField('blogId', $this->company);
        $this->addField('draft', $draft);

        $this->addField('boost', $post['boost']);
        $this->addField('date', \Carbon\Carbon::parse($post['dateTime'])->format('d/m/Y'));
        $this->addField('igDirect', $post['igDirect']);
        $this->addField('igLocation', $post['igLocation']);
        $this->addField('igTags', $post['igTags']);
        $this->addField('iglink', $post['igLink']);
        $this->addField('providers', array_keys($post['providers']));
        $this->addField('networks', 'facebook');

        $this->addField('pictures', $post['mediaUrls']);
        $this->addField('shortener', $post['shortener']);
        $this->addField('text', $post['text']);
        $this->addField('time', \Carbon\Carbon::now()->format('H:i'));
        $this->addField('timezone', $post['timezone']);


        return Http::asForm()->post($this->base . 'api/actions/schedule-new-post', $this->fields)->body();
    }

    public function editDraft2($post, $draft)
    {
        $this->fields = [];
        $this->addField('blogId', $this->company);

        $data = [
            'text' => $post['text'],
            'shortener' => $post['shortener'],
            'iglink' => null,
            'date' => \Carbon\Carbon::parse($post['dateTime'])->format('d/m/Y'),
            'timezone' => $post['timezone'],
            'boost' => $post['boost'],
            'draft' => $draft,
            'tw' => in_array('twitter', array_keys($post['providers'])),
            'fb' => in_array('facebook', array_keys($post['providers'])),
            'ig' => in_array('instagram', array_keys($post['providers'])),
            'in' => in_array('linkedin', array_keys($post['providers'])),
            'gmb' => in_array('gmb', array_keys($post['providers'])),
            'time' => \Carbon\Carbon::parse($post['dateTime'])->format('H:i'),
        ];

        foreach ($post['mediaUrls'] as $index => $image) {
            $data['picture'. $index] = $image;
        }



        $data = [
            [
                'name' => 'text',
                'contents' => $post['text'],
                ],
            [
                'name' => 'shortener',
                'contents' => $post['shortener'],
                ],
            [
                'name' => 'iglink',
                'contents' => null,
                ],
            [
                'name' => 'date',
                'contents' => \Carbon\Carbon::parse($post['dateTime'])->format('d/m/Y'),
                ],
            [
                'name' => 'timezone',
                'contents' => $post['timezone'],
                ],
            [
                'name' => 'boost',
                'contents' => $post['boost'],
                ],
            [
                'name' => 'draft',
                'contents' => $draft,
                ],
            [
                'name' => 'tw',
                'contents' => in_array('twitter', array_keys($post['providers'])),
                ],
            [
                'name' => 'fb',
                'contents' => in_array('facebook', array_keys($post['providers'])),
                ],
            [
                'name' => 'ig',
                'contents' => in_array('instagram', array_keys($post['providers'])),
                ],
            [
                'name' => 'in',
                'contents' => in_array('linkedin', array_keys($post['providers'])),
                ],
            [
                'name' => 'gmb',
                'contents' => in_array('gmb', array_keys($post['providers'])),
                ],
            [
                'name' => 'time',
                'contents' => \Carbon\Carbon::parse($post['dateTime'])->format('H:i'),
                ],
        ];


        

        $boundary = '----WebKitFormBoundarytrURl3lQQkyS4UcC';

        return Http::
            withOptions([
                'headers' => [
                    'Connection' => 'close',
                    'Content-Type' => 'multipart/form-data; boundary='.$boundary,
                ],
                'body' => new \GuzzleHttp\Psr7\MultipartStream($data, $boundary),
            ])
            ->post($this->getUrl('scheduleTweet'))
            ->body();
    }

    public function getAllPosts()
    {
        $this->addField('blogId', $this->company);
        $this->addField('start', 116918100000);
        $this->addField('end', 2017573600000);
        $this->addField('timezone', 'America/Santiago');


        return $this->call('api/actions/programmed-posts');
    }

    public function getAllPostsDraft()
    {
        $this->addField('blogId', $this->company);
        $this->addField('start', 116918100000);
        $this->addField('end', 2017573600000);
        $this->addField('timezone', 'America/Santiago');


        return collect($this->call('api/actions/programmed-posts'))->where('draft', true);
    }

    private function call($url)
    {
        $json = Http::get($this->getUrl($url))->json();

        if (isset($json['errorCode'])) {
            throw new \Exception(json_encode($json));
        }

        return $json;
    }

    private function addField($key, $value)
    {
        $this->fields[$key] = $value;
    }

    private function getUrl($path = null)
    {
        $query = http_build_query(array_merge($this->fields, [
            'userId' => $this->user,
            'userToken' => $this->token
        ]));

        return $this->base . $path . '?' . $query;
    }
}
