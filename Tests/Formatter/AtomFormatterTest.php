<?php
/*
 * This file is part of the Eko\FeedBundle Symfony bundle.
 *
 * (c) Vincent Composieux <vincent.composieux@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eko\FeedBundle\Tests\Formatter;

use Eko\FeedBundle\Feed\FeedManager;
use Eko\FeedBundle\Field\Channel\ChannelField;
use Eko\FeedBundle\Field\Channel\GroupChannelField;
use Eko\FeedBundle\Field\Item\GroupItemField;
use Eko\FeedBundle\Field\Item\ItemField;
use Eko\FeedBundle\Field\Item\MediaItemField;
use Eko\FeedBundle\Formatter\AtomFormatter;
use Eko\FeedBundle\Formatter\FormatterRegistry;
use Eko\FeedBundle\Formatter\RssFormatter;
use Eko\FeedBundle\Tests\Entity\Writer\FakeItemInterfaceEntity;
use Eko\FeedBundle\Tests\Entity\Writer\FakeRoutedItemInterfaceEntity;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * AtomFormatterTest.
 *
 * This is the Atom formatter test class
 *
 * @author Vincent Composieux <vincent.composieux@gmail.com>
 */
class AtomFormatterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FeedManager A feed manager instance
     */
    protected $manager;

    /**
     * Sets up elements used in test case.
     */
    protected function setUp(): void
    {
        $config = [
            'feeds' => [
                'article' => [
                    'title'       => 'My articles/posts',
                    'description' => 'Latests articles',
                    'link'        => 'http://github.com/eko/FeedBundle',
                    'encoding'    => 'utf-8',
                    'author'      => 'Vincent Composieux',
                ],
            ],
        ];

        $translator = $this->createMock(TranslatorInterface::class);

        $formatterRegistry = new FormatterRegistry();
        $formatterRegistry->addFormatter('rss', new RssFormatter($translator, 'test'));
        $formatterRegistry->addFormatter('atom', new AtomFormatter($translator, 'test'));

        $this->manager = new FeedManager($this->getMockRouter(), $config, $formatterRegistry);
    }

    /**
     * Check if exception is an \InvalidArgumentException is thrown
     * when 'author' config parameter is not set or empty.
     */
    public function testAuthorEmptyException()
    {
        $config = [
            'feeds' => [
                'article' => [
                    'title'       => 'My title',
                    'description' => 'My description',
                    'link'        => 'http://github.com/eko/FeedBundle',
                    'encoding'    => 'utf-8',
                    'author'      => '',
                ],
            ],
        ];

        $translator = $this->createMock(TranslatorInterface::class);

        $formatterRegistry = new FormatterRegistry();
        $formatterRegistry->addFormatter('rss', new RssFormatter($translator, 'test'));
        $formatterRegistry->addFormatter('atom', new AtomFormatter($translator, 'test'));

        $manager = new FeedManager($this->getMockRouter(), $config, $formatterRegistry);

        $feed = $manager->get('article');

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Atom formatter requires an "author" parameter in configuration.');

        $feed->set('author', null);
        $feed->render('atom');
    }

    /**
     * Check if Atom formatter output item.
     */
    public function testRenderCorrectRootNodes()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());

        $output = $feed->render('atom');

        $this->assertStringContainsString('<feed xmlns="http://www.w3.org/2005/Atom">', $output);
        $this->assertStringContainsString('<link href="http://github.com/eko/FeedBundle" rel="self" type="application/rss+xml"/>', $output);
    }

    /**
     * Check if Atom formatter output a valid XML.
     */
    public function testRenderValidXML()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());

        $output = $feed->render('atom');

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadXML($output);

        $this->assertEquals(0, count(libxml_get_errors()));
        $this->assertStringContainsString('<feed xmlns="http://www.w3.org/2005/Atom">', $output);
    }

    /**
     * Check if Atom formatter output item.
     */
    public function testRenderItem()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());

        $output = $feed->render('atom');

        $this->assertStringContainsString('<title><![CDATA[Fake title]]></title>', $output);
        $this->assertStringContainsString('<summary type="html"><![CDATA[Fake description or content]]></summary>', $output);
        $this->assertStringContainsString('<link href="http://github.com/eko/FeedBundle/article/fake/url"/>', $output);
    }

    /**
     * Check if a custom channel field is properly rendered.
     */
    public function testAddCustomChannelField()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addChannelField(new ChannelField('fake_channel_custom', 'My fake value'));

        $output = $feed->render('atom');

        $this->assertStringContainsString('<fake_channel_custom>My fake value</fake_channel_custom>', $output);
    }

    /**
     * Check if a custom item field is properly rendered with ItemInterface.
     */
    public function testAddCustomItemFieldWithItemInterface()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addItemField(new ItemField('fake_custom', 'getFeedItemCustom'));

        $output = $feed->render('atom');

        $this->assertStringContainsString('<fake_custom>My custom field</fake_custom>', $output);
    }

    /**
     * Check if a custom item field with one attribute only is properly rendered with ItemInterface.
     */
    public function testAddCustomItemFieldWithOneAttributeOnly()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addItemField(new ItemField('fake_custom', 'getFeedItemCustom', [
            'attribute'      => true,
            'attribute_name' => 'fake-value',
        ]));

        $output = $feed->render('atom');

        $this->assertStringContainsString('<fake_custom fake-value="My custom field"/>', $output);
    }

    /**
     * Check if a custom media item field is properly rendered with ItemInterface.
     */
    public function testAddCustomMediaItemFieldWithItemInterface()
    {
        $fakeEntityWithMedias = new FakeItemInterfaceEntity();
        $fakeEntityWithMedias->setFeedMediaItem([
            'type'   => 'image/jpeg',
            'length' => 500,
            'value'  => 'http://website.com/image.jpg',
        ]);

        $fakeEntityNoMedias = new FakeItemInterfaceEntity();

        $feed = $this->manager->get('article');
        $feed->add($fakeEntityWithMedias);
        $feed->add($fakeEntityNoMedias);
        $feed->addItemField(new MediaItemField('getFeedMediaItem'));

        $output = $feed->render('atom');

        $this->assertStringContainsString('<link rel="enclosure" href="http://website.com/image.jpg" type="image/jpeg" length="500"/>', $output);
    }

    /**
     * Check if a custom group media items field is properly rendered with ItemInterface.
     */
    public function testAddCustomGroupMediaItemsFieldsWithItemInterface()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addItemField(new GroupItemField(
            'images',
            new MediaItemField('getFeedMediaMultipleItems'))
        );

        $output = $feed->render('atom');

        $this->assertStringContainsString('<images>', $output);
        $this->assertStringContainsString('<link rel="enclosure" href="http://website.com/image.jpg" type="image/jpeg" length="500"/>', $output);
        $this->assertStringContainsString('<link rel="enclosure" href="http://website.com/image2.png" type="image/png" length="600"/>', $output);
        $this->assertStringContainsString('</images>', $output);
    }

    /**
     * Check if a custom group item field is properly rendered with ItemInterface.
     */
    public function testAddCustomGroupItemFieldWithItemInterface()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addItemField(new GroupItemField(
            'categories',
            new ItemField('category', 'getFeedCategoriesCustom'))
        );

        $output = $feed->render('atom');

        $this->assertStringContainsString('<categories>', $output);
        $this->assertStringContainsString('<category>category 1</category>', $output);
        $this->assertStringContainsString('<category>category 2</category>', $output);
        $this->assertStringContainsString('<category>category 3</category>', $output);
        $this->assertStringContainsString('</categories>', $output);
    }

    /**
     * Check if a custom group item field with attributes is properly rendered.
     */
    public function testAddCustomGroupItemFieldWithAttributes()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addItemField(
            new GroupItemField(
                'categories',
                new ItemField('category', 'getFeedCategoriesCustom', [], ['category-type' => 'test']),
                ['is-it-test' => 'yes']
            )
        );

        $output = $feed->render('atom');

        $this->assertStringContainsString('<categories is-it-test="yes">', $output);
        $this->assertStringContainsString('<category category-type="test">category 1</category>', $output);
        $this->assertStringContainsString('<category category-type="test">category 2</category>', $output);
        $this->assertStringContainsString('<category category-type="test">category 3</category>', $output);
        $this->assertStringContainsString('</categories>', $output);
    }

    /**
     * Check if a custom group item field can contain another group items field.
     */
    public function testAddCustomGroupItemFieldContainingAnotherGroup()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addItemField(
            new GroupItemField(
                'links',
                array(
                    new GroupItemField('link', new ItemField('category', 'getFeedCategoriesCustom')),
                    new GroupItemField('link', new ItemField('category', 'getFeedCategoriesCustom'))
                )
            )
        );

        $output = $feed->render('atom');
        $output = str_replace(array("\r", "\n", "  "), '', $output);

        $this->assertStringContainsString('<links><link><category>category 1</category><category>category 2</category><category>category 3</category></link><link><category>category 1</category><category>category 2</category><category>category 3</category></link></links>', $output);
    }

    /**
     * Check if a custom group item field with attributes from method is properly rendered.
     */
    public function testAddCustomGroupItemFieldWithAttributesFromMethod()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addItemField(
            new GroupItemField(
                'categories',
                new ItemField('category', 'getFeedCategoriesCustom', [], ['getItemKeyAttribute' => 'getItemValueAttribute']),
                ['getGroupKeyAttribute' => 'getGroupValueAttribute']
            )
        );

        $output = $feed->render('atom');

        $this->assertStringContainsString('<categories my-group-key-attribute="my-group-value-attribute">', $output);
        $this->assertStringContainsString('<category my-item-key-attribute="my-item-value-attribute">category 1</category>', $output);
        $this->assertStringContainsString('<category my-item-key-attribute="my-item-value-attribute">category 2</category>', $output);
        $this->assertStringContainsString('<category my-item-key-attribute="my-item-value-attribute">category 3</category>', $output);
        $this->assertStringContainsString('</categories>', $output);
    }

    /**
     * Check if a custom group channel field is properly rendered with GroupFieldInterface.
     */
    public function testAddCustomGroupChannelFieldWithItemInterface()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addChannelField(
            new GroupChannelField('image', [
                new ChannelField('name', 'My test image'),
                new ChannelField('url', 'http://www.example.com/image.jpg'),
            ])
        );

        $output = $feed->render('atom');

        $this->assertStringContainsString('<image>', $output);
        $this->assertStringContainsString('<name>My test image</name>', $output);
        $this->assertStringContainsString('<url>http://www.example.com/image.jpg</url>', $output);
        $this->assertStringContainsString('</image>', $output);
    }

    /**
     * Check if a custom group channel field is properly rendered with attributes.
     */
    public function testAddCustomGroupChannelFieldWithAttributes()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addChannelField(
            new GroupChannelField('image', [
                new ChannelField('name', 'My test image', [], ['name-attribute' => 'test']),
                new ChannelField('url', 'http://www.example.com/image.jpg', [], ['url-attribute' => 'test']),
            ], ['image-attribute' => 'test'])
        );

        $output = $feed->render('atom');

        $this->assertStringContainsString('<image image-attribute="test">', $output);
        $this->assertStringContainsString('<name name-attribute="test">My test image</name>', $output);
        $this->assertStringContainsString('<url url-attribute="test">http://www.example.com/image.jpg</url>', $output);
        $this->assertStringContainsString('</image>', $output);
    }

    /**
     * Check if a custom group item field with multiple item fields is properly rendered with ItemInterface.
     */
    public function testAddCustomGroupMultipleItemFieldWithItemInterface()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeItemInterfaceEntity());
        $feed->addItemField(new GroupItemField('author', [
            new ItemField('name', 'getFeedItemAuthorName', ['cdata' => true]),
            new ItemField('email', 'getFeedItemAuthorEmail'),
        ]));

        $output = $feed->render('atom');

        $this->assertStringContainsString(<<<'EOF'
    <author>
      <name><![CDATA[John Doe]]></name>
      <email>john.doe@example.org</email>
    </author>
EOF
            , $output);
    }

    /**
     * Check if a custom item field is properly rendered with RoutedItemInterface.
     */
    public function testAddCustomItemFieldWithRoutedItemInterface()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeRoutedItemInterfaceEntity());
        $feed->addItemField(new ItemField('fake_custom', 'getFeedItemCustom'));

        $output = $feed->render('atom');

        $this->assertStringContainsString('<fake_custom>My custom field</fake_custom>', $output);
    }

    /**
     * Check if anchors are really appended to generated url of RouterItemInterface.
     */
    public function testAnchorIsAppendedToLinkWithRoutedItemInterface()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeRoutedItemInterfaceEntity());

        $output = $feed->render('atom');
        $this->assertStringContainsString('<link href="http://github.com/eko/FeedBundle/article/fake/url#fake-anchor"/>', $output);
    }

    /**
     * Check if an exception is thrown when trying to render a non-existant method with RoutedItemInterface.
     */
    public function testNonExistantCustomItemFieldWithRoutedItemInterface()
    {
        $feed = $this->manager->get('article');
        $feed->add(new FakeRoutedItemInterfaceEntity());
        $feed->addItemField(new ItemField('fake_custom', 'getFeedDoNotExistsItemCustomMethod'));

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Method "getFeedDoNotExistsItemCustomMethod" should be defined in your entity.');

        $feed->render('atom');
    }

    /**
     * Check if values are well translated with "translatable" option.
     */
    public function testTranslatableValue()
    {
        $config = [
            'feeds' => [
                'article' => [
                    'title'       => 'My title',
                    'description' => 'My description',
                    'link'        => 'http://github.com/eko/FeedBundle',
                    'encoding'    => 'utf-8',
                    'author'      => 'Vincent',
                ],
            ],
        ];

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects($this->any())->method('trans')->will($this->returnValue('translatable-value'));

        $formatterRegistry = new FormatterRegistry();
        $formatterRegistry->addFormatter('atom', new AtomFormatter($translator, 'test'));

        $manager = new FeedManager($this->getMockRouter(), $config, $formatterRegistry);

        $feed = $manager->get('article');
        $feed->add(new FakeRoutedItemInterfaceEntity());
        $feed->addItemField(new ItemField('fake_custom', 'getFeedItemCustom', [
            'translatable' => true,
        ]));

        $output = $feed->render('atom');
        $this->assertStringContainsString('<fake_custom>translatable-value</fake_custom>', $output);
    }

    /**
     * Returns RouterInterface mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockRouter()
    {
        $mockRouter = $this->createMock(RouterInterface::class);

        $mockRouter->expects($this->any())
            ->method('generate')
            ->with('fake_route', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->will($this->returnValue('http://github.com/eko/FeedBundle/article/fake/url'));

        return $mockRouter;
    }
}
