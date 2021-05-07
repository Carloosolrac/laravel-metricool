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

    public function removePost($id)
    {
        $this->fields = [];
        $this->addField('blogId', (int)$this->company);
        $this->addField('id', $id['id']);

        return Http::get($this->getUrl('api/actions/deleteProgrammedTW'))->body();
    }

    public function editDraft($post, $draft)
    {
        $this->fields = [];
        $this->addField('userId', (int)$this->user);
        $this->addField('userToken', $this->token);
        $this->addField('blogId', (int)$this->company);
        $this->addField('draft', $draft);

        $this->addField('date', \Carbon\Carbon::parse($post['dateTime'])->format('d/m/Y'));

        $this->addField('boost', $post['boost']);
        $this->addField('igDirect', $post['igDirect']);
        $this->addField('igLocation', $post['igLocation']);
        $this->addField('igTags', $post['igTags']);
        $this->addField('iglink', $post['igLink']);

        $this->addField('networks', array_keys($post['providers']));

        $this->addField('pictures', $post['mediaUrls']);
        // $this->addField('pictures', ['http://newsletter.muycomputer.com/muypymes/ebook-email-marketing-web.pdf']);
        $this->addField('shortener', $post['shortener']);
        $this->addField('text', $post['text']);
        $this->addField('time', \Carbon\Carbon::parse($post['dateTime'])->format('H:i'));
        $this->addField('timezone', $post['timezone']);


        // return $this->base . 'api/actions/schedule-new-post';


        return Http::asForm()->post($this->getUrl('api/actions/schedule-new-post'))->body();
    }

    public function getAllPosts()
    {
        $this->addField('blogId', $this->company);
        $this->addField('start', 116918100000);
        $this->addField('end', 2017573600000);
        $this->addField('timezone', 'America/Santiago');


        return $this->call('api/actions/scheduled-posts');
    }

    public function getAllPostsDraft()
    {
        return collect($this->getAllPosts())->where('draft', true);
    }

    public function getPendings()
    {
        return collect($this->getAllPosts())->where('draft', false)->filter(function ($post) {
            foreach ($post['providers'] as $provider) {
                if ($provider['status'] === 'PENDING') {
                    return true;
                }
            }
        });
    }

    public function getPublished()
    {
        return collect($this->getAllPosts())->where('draft', false)->filter(function ($post) {
            foreach ($post['providers'] as $provider) {
                if ($provider['status'] === 'PUBLISHED') {
                    return true;
                }
            }
        });
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

        $query = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);


        return $this->base . $path . '?' . $query;
    }
}
