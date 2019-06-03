<?php

namespace Tests\Feature;

use Tests\Support\{Prepare, GroupTrait, ItemTrait, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;

class ItemTest extends Prepare
{

    use UserTrait;
    use GroupTrait;
    use ItemTrait;
	
    protected $url;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->url = $this->getItemCreateUrl($this->getGroup()->id);
    }

    public function testNotAuthenticatedUserCanNotSeeItemSearchPage(): void
    {
        $response = $this->get($this->url);
        $response->assertRedirect('/login');
    }
    
    public function testUserCanNotSeeItemSearchPageWithoutGroup(): void
    {
        $response = $this->actingAs($this->getUser())->get($this->getItemCreateUrl());
        $response->assertStatus(404);
    }
    
    public function testAuthenticatedUserCanSeeItemSearchPage(): void
    {
        $response = $this->actingAs($this->getUser())->get($this->url);
        $response->assertOk();
    }
    
    public function testUserCanNotFindItemWithoutSid(): void
    {
        $response = $this->executeQuery(
            $this->url, 
            $this->getQueryData(null, $this->getGroup()->id)
        );
        $response->assertRedirect($this->url);
        $response->assertSessionHasErrors('sid');
	}
    
    public function testUserCanNotFindItemWithoutGroupId()
    {
        $response = $this->executeQuery(
            $this->url, 
            $this->getQueryData($this->getItem()->sid, null)
        );
        $response->assertRedirect($this->url);
        $response->assertSessionHasErrors('group');
    }
    
    public function testUserCanNotFindItemWithWrongSid()
    {
        $response = $this->from($this->url)
            ->actingAs($this->getUser())
            ->post('/item/show', [
                'sid' => '1',
                'group' => $this->getGroup()->id
            ]);
        $response->assertSessionHasErrors(0);
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
        $response->assertRedirect('/');
    }
    
    public function testUserCanFindItem()
    {
        $response = $this->executeQuery(
            $this->url, 
            $this->getQueryData($this->getItem()->sid, $this->getGroup()->id)
        );
        $response->assertOk();
        $response->assertViewIs('orders.create.exists');
        $response->assertSee('Количество');
    }
    
    protected function getQueryData(int $sid = null, int $group = null): array
    {
        return [
            'sid' => $sid,
            'group' => $group
        ];
    }
    
    protected function executeQuery(string $url, array $data): Response
    {
        return $this->from($url)
            ->actingAs($this->getUser())
            ->post('/item/show', $data);
    }
    
    protected function getItemCreateUrl(int $group = NULL): string
    {
        return '/item/create/'.$group;
    }
    
}