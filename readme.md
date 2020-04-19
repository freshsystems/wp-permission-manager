# WP Permission Manager

**A user role management plugin for WordPress that puts you in full control of your site's permissions.**

This plugin is forked from the last release of the popular [justintadlock/members](https://github.com/justintadlock/members) plugin, before it's codebase was hijacked and turned into a sales pitch for a premium plugin by it's new maintainers. 

![Screenshot](screenshot-2.png)

---

### Plugin Features

* **Role Manager:** Allows you to edit, create, and delete roles as well as capabilities for these roles.
* **Multiple User Roles:** Give one, two, or even more roles to any user.
* **Explicitly Deny Capabilities:** Deny specific caps to specific user roles.
* **Clone Roles:** Build a new role by cloning an existing role.

In order to refocus the plugin's functionality, the following features from the original plugin were removed in [`v3.0.0`](https://github.com/freshsystems/wp-permission-manager/releases/tag/3.0.0). If you still need these features, you can use the [`v2.2.1`](https://github.com/freshsystems/wp-permission-manager/releases/tag/2.2.1) release, or another version of this plugin.

* ~~**Content Permissions:** Gives you control over which users (by role) have access to post content.~~
* ~~**Shortcodes:** Shortcodes to control who has access to content.~~
* ~~**Widgets:**  A login form widget and users widget to show in your theme's sidebars.~~
* ~~**Private Site:** You can make your site and its feed completely private if you want.~~

### Installation

```
composer require freshsystems/wp-permission-manager:^3
```

If you're not [using Composer with WordPress](https://roots.io/bedrock-vs-regular-wordpress-install/), you can also download the ZIP archive from the releases page and upload/install the plugin manually. The plugin will installable through the official WordPress Plugin Directory after significant refactoring in `v4.0.0`.

### Roadmap

<!-- Also see the release [milestones](https://github.com/freshsystems/wp-permission-manager/milestones). -->

#### [`v2.2.1`](https://github.com/freshsystems/wp-permission-manager/releases/tag/2.2.1)

- [x] ~~Update Composer/package details.~~
- [x] ~~Update UI visible plugin and menu names (`Members` → `WP Permission Manager`).~~
- [x] ~~Remove non-functional admin settings pages (i.e. 'Donate').~~

#### [`v3.0.0`](https://github.com/freshsystems/wp-permission-manager/releases/tag/3.0.0)

- [x] ~~Remove all 'Shortcodes'.~~
- [x] ~~Remove all 'Widgets'.~~
- [x] ~~Remove all 'Content Permission' functionality.~~
- [x] ~~Remove 'Private Site' functionality.~~
- [x] ~~Remove all 'add-ons' functionality.~~
- [x] ~~Make the core 'Role Manager' always enabled.~~
- [x]  ~~Bump minumum PHP version requirement to `>= 5.6.20`.~~

#### `v4.0.0`

- [ ] Replace remaining "members" terminology (translation text-domain, slug use, etc.).
- [ ] Maybe incorporate relevant add-ons (license dependant) into the plugin's core.
- [ ] Replace the plugin's namespace, e.g. `Members\` → `Fresh\PermissionManager\`.
- [ ] Replace the prefix for all filter/action hook names and functions (deprecate the originals for permanent backwards-compatibility), e.g. `members_register_cap_group()` → `Fresh\PermissionManager\register_cap_group()`, `members_register_cap_groups` → `wppm_register_cap_groups`.
- [ ] Consider implementing some enhancements proposed in [justintadlock/members - 3.0.0 Milestone](https://github.com/justintadlock/members/milestone/5).
- [ ] Update `readme.md` documentation and add `readme.txt` files.
- [ ] Publish to the WordPress Plugin Directory.

---

## Documentation

### Role management

The Role Manager feature allows you to edit and add new roles as well as add and remove both default capabilities and custom capabilities from roles.  It is an extremely powerful system.

Any changes you make to users and roles using this feature are permanent changes.  What I mean by this is that if you deactivate or uninstall this plugin, the changes won't revert to their previous state.  This plugin merely provides a user interface for you to make changes directly to your WordPress database.  Please use this feature wisely.

#### Editing/Adding Roles

This feature can be both a blessing and a curse, so I'm going to ask that you use it wisely.  Use extreme caution when assigning new capabilities to roles. You wouldn't want to grant Average Joe the `edit_plugins` capability, for example.

You can find the settings page for this feature under the "Users" menu.  It will be labeled "Roles".  When clicking on the menu item, you'll be take to a screen similar to the edit post/page screen, only it'll be for editing a role.

In the "Edit Capabilities" box on that screen, you simply have to tick the checkbox next to the capability you want to grant or deny.

#### Grant, deny, or neither?

Every capability can have one of three "states" for a role.  The role can be *granted*, *denied*, or simply not have a capability.

* **Granting** a capability to a role means that users of that role will have permission to perform the given capability.
* **Denying** a capability means that the role's users are explicitly denied permission.
* A role that is neither granted nor denied a capability simply doesn't have that capability.

**Note:** When assigning multiple roles to a single user that have a conflicting capability (e.g., granted `publish_posts` and denied `published_posts` cap), it's best to enable the denied capabilities override via the Members Settings screen.  This will consistently make sure that denied capabilities always overrule granted capabilities.  With this setting disabled, WordPress will decide based on the *last* role given to the user, which can mean for extremely inconsistent behavior depending on the roles a user has.

#### How denied capabilities work

Suppose the **Super** role is *granted* these capabilities:

* `edit_posts`

Then, suppose the **Duper** role is *granted* these capabilities:

- `publish_posts`
- `edit_products`

Now, further suppose **User A** has the **Super** role because you want them to edit posts.  However, you also want **User A** to be able to edit products so you assign them the **Duper** role.  Suddenly, **User A** is *granted* the following capabilities:

- `edit_posts`
- `publish_posts`
- `edit_products`

For whatever reason you don't ever want users with the **Super** role to be able to publish posts.  Now you have a problem.  One way to solve this is to create a third role with just the caps that you want and give that single role to **User A**.  However, that becomes cumbersome on larger sites with many roles.  

Instead, you could explicitly *deny* the publish posts capability to the **Super** role.  When you do that, **User A** is only *granted* the following capabilities:

- `edit_posts`
- `edit_products`

And is denied the following capabilities:

- `publish_posts`

### Multiple user roles

You can assign a user more than one role by going to that edit user screen in the admin and locating the "Roles" section.  There will be a checkbox for every role.

You can also multiple roles to a user from the add new user screen.

On the "Users" screen in the admin, you can bulk add or remove single roles from multiple users.

### Checking if the current user has a capability

In plugins and your theme template files, you might sometimes need to check if the currently logged in user has permission to do something.  We do this by using the WordPress function `current_user_can()`.  The basic format looks like this:

	<?php if ( current_user_can( 'capability_name' ) ) echo 'This user can do something'; ?>

For a more practical situation, let's say you created a new capability called `read_pages`.  Well, you might want to hide the content within your `page.php` template by adding this:

	<?php if ( current_user_can( 'read_pages ' ) ) : ?>
		<?php the_content(); ?>
	<?php endif; ?>

Only users with a role that has the `read_pages` capability will be able to see the content.

### Checking if a user has a role

Before beginning, I want to note that you really shouldn't do this.  It's better to check against capabilities.  However, for those times when you need to break the rules, you can do so like:

	if ( members_user_has_role( $user_id, $role ) )

Or, you can check against the current user:

	if ( members_current_user_has_role( $role ) )

### Need the old user levels system?

Some plugins and themes might rely on the old user level system in WordPress.  These were deprecated in WordPress version 2.1 and should not be used at all.  WordPress still has minimal legacy support for these, but I highly suggest contacting your theme/plugin author if user levels are being used.

By default, the levels aren't shown.  They still exist, but are tucked away behind the scenes.  While not recommended, if you need to control who has what level (levels are just capabilities), add this to your plugin or your theme's `functions.php`:

	add_filter( 'members_remove_old_levels', '__return_false' );

### Registering capabilities

If you're a plugin developer with custom capabilities, beginning with version 2.0.0 of Members, you can register your capabilities with Members.  Essentially, this allows users to see your capabilities in a nicely-formatted, human-readable form (e.g., `Publish Posts` instead of `publish_posts`).  This also means that it can be translated so that it's easier to understand for users who do not read English.

	add_action( 'members_register_caps', 'th_register_caps' );

	function th_register_caps() {

		members_register_cap(
			'your_cap_name',
			array(
				'label' => __( 'Your Capability Label', 'example-textdomain' ),
				'group' => 'example'
			)
		);
	}

The `group` argument is not required, but will allow you to assign the capability to a cap group.

### Registering cap groups

Members groups capabilities so that users can more easily find them when editing roles.  If your plugin has multiple capabilities, you should consider creating a custom cap group.

	add_action( 'members_register_cap_groups', 'th_register_cap_groups' );

	function th_register_cap_groups() {

		members_register_cap_group(
			'your_group_name',
			array(
				'label'    => __( 'Your Group Label', 'example-textdomain' ),
				'caps'     => array(),
				'icon'     => 'dashicons-admin-generic',
				'priority' => 10
			)
		);
	}

The arguments for the array are:

* `label` - An internationalized text label for your group.
* `caps` - An array of initial capabilities to add to your group.
* `icon` - The name of one of core WP's [dashicons](https://developer.wordpress.org/resource/dashicons/) or a custom class (would need to be styled by your plugin in this case).
* `priority` - The priority of your group compared to other groups.  `10` is the default.

_Note that custom post types are automatically registered as groups with Members.  So, if you want to do something custom with that, you simply need to unregister the group before registering your own._

	members_unregister_cap_group( "type-{$post_type}" );

---

### Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.
 
Original work Copyright © 2009 - 2018, [Justin Tadlock](http://justintadlock.com).
Modified work Copyright © 2020, [Fresh Systems Ltd](https://freshsystems.co.uk).
