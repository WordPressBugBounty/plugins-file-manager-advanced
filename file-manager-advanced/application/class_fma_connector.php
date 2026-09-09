<?php
/**
 * @package: File Manager Advanced
 * @Class: fma_connector
 */
if ( class_exists( 'class_fma_connector' ) ) {
    return;
}
class class_fma_connector {
    // elfinder defaults:
    //read:https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
    public function fma_local_file_system() {
        $settings = get_option('fmaoptions');
        $is_admin = class_fma_permissions::has_unrestricted_filesystem_access();

        // Administrators keep Public Root Path / ABSPATH (unchanged behaviour).
        $path = ABSPATH;
        $url  = site_url();

        if ( isset( $settings['public_path'] ) && ! empty( $settings['public_path'] ) ) {
            $path = $settings['public_path'];
        }

        if ( isset( $settings['public_url'] ) && ! empty( $settings['public_url'] ) ) {
            $url = $settings['public_url'];
        }

        if ( ! $is_admin ) {
            $restricted = class_fma_permissions::resolve_restricted_root(
                isset( $settings['public_path'] ) ? $settings['public_path'] : '',
                isset( $settings['public_url'] ) ? $settings['public_url'] : ''
            );
            $path = $restricted['path'];
            $url  = $restricted['url'];
        }

		// Keep elFinder file links valid when an old/default URL was saved.
		$root_path = untrailingslashit( wp_normalize_path( $path ) );
		$base_path = untrailingslashit( wp_normalize_path( ABSPATH ) );
		$root_is_inside_site = $root_path === $base_path || 0 === strpos( $root_path . '/', trailingslashit( $base_path ) );

		if ( $root_is_inside_site ) {
			$root_relative_path = ltrim( substr( $root_path, strlen( $base_path ) ), '/' );
			$url = '' === $root_relative_path ? site_url() : trailingslashit( site_url() ) . $root_relative_path;
		}

        if ( !function_exists( 'elFinderAutoloader' ) ) {
            require 'library/php/autoload.php';
        }

        // Load custom filesystem driver for content search
        if (defined('FMAFILEPATH')) {
            require_once FMAFILEPATH . 'application/class_fma_local_filesystem.php';
        } else {
            require_once dirname(__FILE__) . '/class_fma_local_filesystem.php';
        }

        if ( isset( $settings['enable_trash'] ) && ($settings['enable_trash'] == '1' ) ) {
            $trash   = array(
                'id'            => '1',
                'driver'        => 'Trash',
                'path'          => FMAFILEPATH.'application/library/files/.trash/',
                'tmbURL'        => site_url() . '/application/library/files/.trash/.tmb/',
                'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                'uploadDeny'    => array(''),                // Recomend the same settings as the original volume that uses the trash
                'uploadAllow'   => array('all'),// Same as above
                'uploadOrder'   => array('deny', 'allow'),      // Same as above
                'accessControl' => 'access',                    // Same as above
                'attributes'    => array(
                    array(
                        'pattern' => '/.tmb/',
                        'read'    => false,
                        'write'   => false,
                        'hidden'  => true,
                        'locked'  => false,
                    ),
                    array(
                        'pattern' => '/.quarantine/',
                        'read'    => false,
                        'write'   => false,
                        'hidden'  => true,
                        'locked'  => false,
                    ),
                    array(
                        'pattern' => '/.gitkeep/',
                        'read'    => false,
                        'write'   => false,
                        'hidden'  => true,
                        'locked'  => false,
                    )
                ),
            );
            $trash_f = 't1_Lw';
        } else {
            $trash   = array();
            $trash_f = '';
        }

        $hide_htaccess = array(
            'pattern' => '/.htaccess/',
            'read'    => false,
            'write'   => false,
            'hidden'  => true,
            'locked'  => false,
        );

        if ( isset( $settings['enable_htaccess'] ) && ! empty( $settings['enable_htaccess'] ) && $settings['enable_htaccess'] == '1' ) {
            $hide_htaccess = array(
                'pattern' => '/.htaccess/',
                'read'    => true,
                'write'   => false,
                'hidden'  => false,
                'locked'  => false,
            );
        }

        // getting allowed upload
        $allowUpload = array( 'all' );

        if ( isset( $settings['fma_upload_allow'] ) && ! empty( $settings['fma_upload_allow'] ) ) {
            $allowUpload = explode( ',',$settings['fma_upload_allow'] );
        }

        // restricting max upload size
        $max_upload_size = isset( $settings['upload_max_size'] ) ? $settings['upload_max_size']  : '0';

        $opts = array(
            'roots' => array(
                // Items volume
                array(
                    'driver'        => 'fma_local_filesystem',           // custom driver with content search support
                    'path'          => $path,                 // path to files (REQUIRED)
                    'URL'           => $url, // URL to files (REQUIRED)
                    'trashHash'     => $trash_f,                     // elFinder's hash of trash folder
                    'winHashFix'    => DIRECTORY_SEPARATOR !== '/', // to make hash same to Linux one on windows too
                    'uploadDeny'    => $is_admin ? array('all') : class_fma_permissions::get_restricted_upload_deny_mimes(),
                    'uploadAllow'   => $allowUpload,// Mimetype `image` and `text/plain` allowed to upload
                    'uploadOrder'   => $is_admin ? array('deny','allow') : array('allow', 'deny'),
                    'disabled'      => array('help','preference'),
                    'accessControl' => 'access',
                    'acceptedName'  => $is_admin ? '' : 'afm_plugin_file_validName',
                    'uploadMaxSize' => $max_upload_size,
                    'searchTimeout' => 300,
                    'attributes'    => array(
                        array(
                            'pattern' => '/.tmb/',
                            'read'    => false,
                            'write'   => false,
                            'hidden'  => true,
                            'locked'  => false,
                        ),
                        array(
                            'pattern' => '/.quarantine/',
                            'read'    => false,
                            'write'   => false,
                            'hidden'  => true,
                            'locked'  => false,
                        ),
                        array(
                            'pattern' => '/.gitkeep/',
                            'read'    => false,
                            'write'   => false,
                            'hidden'  => true,
                            'locked'  => false,
                        ),
                        $hide_htaccess
                    ),
                ),
                // Trash volume
                $trash,
            ),
        );

        if ( ! $is_admin ) {
            $opts['roots'][0]['attributes'] = array_merge(
                $opts['roots'][0]['attributes'],
                class_fma_permissions::get_restricted_file_attributes()
            );
            // Same filename policy on every read/write path (not only get/put).
            $opts['bind']['put.pre']     = array( $this, 'on_put_command' );
            $opts['bind']['get.pre']     = array( $this, 'on_get_command' );
            $opts['bind']['file.pre']    = array( $this, 'on_file_command' );
            $opts['bind']['zipdl.pre']   = array( $this, 'on_zipdl_command' );
            $opts['bind']['archive.pre'] = array( $this, 'on_archive_command' );
            $opts['bind']['rm.pre']      = array( $this, 'on_rm_command' );
            $opts['bind']['rename.pre']  = array( $this, 'on_rename_command' );
        }

		// SVG sanitiser + post-write policy for every command that can create/change files
		// (AFM-885 WPScan: extract bypassed upload/put-only binds; rename/content sniff follow-up).
		$opts['bind']['upload']    = array( $this, 'on_files_written_event' );
		$opts['bind']['extract']   = array( $this, 'on_files_written_event' );
		$opts['bind']['duplicate'] = array( $this, 'on_files_written_event' );
		$opts['bind']['paste']     = array( $this, 'on_files_written_event' );
		$opts['bind']['put']       = array( $this, 'on_put_event' );
		$opts['bind']['rename']    = array( $this, 'on_rename_event' );
		$opts['bind']['search.pre'] = array( $this, 'on_search_command' );
        $opts = apply_filters( 'fma__opts_override', $opts );

        // So cloud drivers building onetime/temp URLs hit the WP ajax connector.
        if ( ! defined( 'ELFINDER_CONNECTOR_URL' ) ) {
            define(
                'ELFINDER_CONNECTOR_URL',
                add_query_arg(
                    array(
                        'action'  => 'fma_load_fma_ui',
                        '_fmakey' => wp_create_nonce( 'fmaskey' ),
                    ),
                    admin_url( 'admin-ajax.php' )
                )
            );
        }

        // run elFinder
        $fma_connector = fma_create_elfinder_connector(new elFinder($opts));
        try {
            $fma_connector->run();
            die;
        } catch ( Exception $e ) {

        }
    }

	/**
	 * Block restricted overwrite operations for non-administrator users.
	 */
	public function on_put_command( $cmd, &$args, $elfinder, $volume ) {
		if ( empty( $args['target'] ) || ! $volume ) {
			return;
		}

		$file = $volume->file( $args['target'] );
		if ( ! $file || empty( $file['name'] ) ) {
			return;
		}

		if ( ! class_fma_permissions::is_restricted_write_filename_allowed( $file['name'] ) ) {
			return array(
				'preventexec' => true,
				'results'     => array(
					'error' => array( elFinder::ERROR_UPLOAD_FILE_MIME ),
				),
			);
		}
	}

	/**
	 * Block restricted editor read operations (get cmd for code editor).
	 */
	public function on_get_command( $cmd, &$args, $elfinder, $volume ) {
		if ( empty( $args['target'] ) || ! $volume ) {
			return;
		}

		$file = $volume->file( $args['target'] );
		if ( ! $file || empty( $file['name'] ) ) {
			return;
		}

		if ( ! class_fma_permissions::is_restricted_write_filename_allowed( $file['name'] ) ) {
			return array(
				'preventexec' => true,
				'results'     => array(
					'error' => array( elFinder::ERROR_ACCESS_DENIED ),
				),
			);
		}
	}

	/**
	 * Block restricted single file download operations (file cmd).
	 * Allows downloading PHP files for authorized non-admin roles (AFM-989).
	 */
	public function on_file_command( $cmd, &$args, $elfinder, $volume ) {
		if ( empty( $args['target'] ) || ! $volume ) {
			return;
		}

		$file = $volume->file( $args['target'] );
		if ( ! $file || empty( $file['name'] ) ) {
			return;
		}

		if ( ! class_fma_permissions::is_restricted_download_filename_allowed( $file['name'] ) ) {
			return array(
				'preventexec' => true,
				'results'     => array(
					'error' => array( elFinder::ERROR_ACCESS_DENIED ),
				),
			);
		}
	}

	/**
	 * Block zip download of restricted filenames.
	 * Allows downloading PHP files in zip archives for authorized non-admin roles (AFM-989).
	 */
	public function on_zipdl_command( $cmd, &$args, $elfinder, $volume ) {
		if ( empty( $args['targets'] ) || ! is_array( $args['targets'] ) || ! $volume ) {
			return;
		}

		foreach ( $args['targets'] as $target ) {
			$file = $volume->file( $target );
			if ( ! $file || empty( $file['name'] ) ) {
				continue;
			}
			if ( ! empty( $file['mime'] ) && 'directory' === $file['mime'] ) {
				// Directory zipdl is handled by archive.pre / volume locks.
				continue;
			}
			if ( ! class_fma_permissions::is_restricted_download_filename_allowed( $file['name'] ) ) {
				return array(
					'preventexec' => true,
					'results'     => array(
						'error' => array( elFinder::ERROR_ACCESS_DENIED ),
					),
				);
			}
		}
	}

	/**
	 * Refuse archiving targets that are locked/hidden or have restricted names.
	 */
	public function on_archive_command( $cmd, &$args, $elfinder, $volume ) {
		if ( empty( $args['targets'] ) || ! is_array( $args['targets'] ) || ! $volume ) {
			return;
		}

		foreach ( $args['targets'] as $target ) {
			$file = $volume->file( $target );
			if ( ! $file ) {
				continue;
			}
			if ( ! empty( $file['locked'] ) || ! empty( $file['hidden'] ) ) {
				return array(
					'preventexec' => true,
					'results'     => array(
						'error' => array( elFinder::ERROR_PERM_DENIED ),
					),
				);
			}
			if ( ! empty( $file['name'] ) && ! class_fma_permissions::is_restricted_write_filename_allowed( $file['name'] ) ) {
				return array(
					'preventexec' => true,
					'results'     => array(
						'error' => array( elFinder::ERROR_PERM_DENIED ),
					),
				);
			}
			// Directories: block if any locked/hidden descendant exists (incl. .php).
			if ( ! empty( $file['mime'] ) && 'directory' === $file['mime'] && method_exists( $volume, 'closest' ) ) {
				$locked = $volume->closest( $target, 'locked', true );
				if ( $locked ) {
					return array(
						'preventexec' => true,
						'results'     => array(
							'error' => array( elFinder::ERROR_PERM_DENIED ),
						),
					);
				}
			}
		}
	}

	/**
	 * Refuse recursive directory delete when locked/hidden children exist.
	 */
	public function on_rm_command( $cmd, &$args, $elfinder, $volume ) {
		if ( empty( $args['targets'] ) || ! is_array( $args['targets'] ) || ! $volume ) {
			return;
		}

		foreach ( $args['targets'] as $target ) {
			$file = $volume->file( $target );
			if ( ! $file ) {
				continue;
			}
			if ( ! empty( $file['locked'] ) ) {
				return array(
					'preventexec' => true,
					'results'     => array(
						'error' => array( elFinder::ERROR_LOCKED, $file['name'] ),
					),
				);
			}
			if ( ! empty( $file['mime'] ) && 'directory' === $file['mime'] && method_exists( $volume, 'closest' ) ) {
				$locked = $volume->closest( $target, 'locked', true );
				if ( $locked ) {
					return array(
						'preventexec' => true,
						'results'     => array(
							'error' => array( elFinder::ERROR_PERM_DENIED ),
						),
					);
				}
			}
		}
	}

	/**
	 * Block renaming to a restricted filename for non-administrators.
	 */
	public function on_rename_command( $cmd, &$args, $elfinder, $volume ) {
		if ( empty( $args['name'] ) ) {
			return;
		}

		if ( ! class_fma_permissions::is_restricted_write_filename_allowed( $args['name'] ) ) {
			return array(
				'preventexec' => true,
				'results'     => array(
					'error' => array( elFinder::ERROR_UPLOAD_FILE_MIME ),
				),
			);
		}
	}

	/**
	 * Sanitize SVG after put (mkfile+put path skips upload sanitiser).
	 * Content-based: SVG payload in a non-.svg name is still sanitised so a
	 * later rename to .svg cannot revive script tags (AFM-885 residual).
	 */
	public function on_put_event( $cmd, &$result, $args, $elfinder, $volume = null ) {
		if ( 'put' !== $cmd || empty( $args['target'] ) || ! $volume ) {
			return;
		}

		$file = $volume->file( $args['target'] );
		if ( ! $file || empty( $file['name'] ) ) {
			return;
		}

		$content_hint = isset( $args['content'] ) ? $args['content'] : null;
		if ( $this->should_sanitize_as_svg( $file, $volume, $content_hint ) ) {
			$this->sanitize_svg_file_content( $file, $volume );
		}
	}

	/**
	 * After rename: sanitise if the new name is .svg or contents look like SVG.
	 * Closes: put malicious SVG into .txt → rename to .svg.
	 */
	public function on_rename_event( $cmd, &$result, $args, $elfinder, $volume = null ) {
		if ( 'rename' !== $cmd || ! $volume ) {
			return;
		}

		$candidates = array();
		if ( ! empty( $result['added'] ) && is_array( $result['added'] ) ) {
			$candidates = array_merge( $candidates, $result['added'] );
		}
		if ( ! empty( $result['changed'] ) && is_array( $result['changed'] ) ) {
			$candidates = array_merge( $candidates, $result['changed'] );
		}

		foreach ( $candidates as $file ) {
			if ( empty( $file['hash'] ) || empty( $file['name'] ) ) {
				continue;
			}
			if ( ! empty( $file['mime'] ) && 'directory' === $file['mime'] ) {
				continue;
			}
			if ( $this->should_sanitize_as_svg( $file, $volume ) ) {
				$this->sanitize_svg_file_content( $file, $volume );
			}
		}
	}

	/**
	 * Post-write handler for upload / extract / duplicate / paste (AFM-885).
	 *
	 * elFinder passes ($cmd, &$result, $args, $elfinder, $dstVolume).
	 * Apply SVG sanitiser to every newly created file (including nested extract),
	 * and for non-admins remove entries that fail the filename policy.
	 */
	public function on_files_written_event( $cmd, &$result, $args, $elfinder, $volume = null ) {
		if ( empty( $result['added'] ) || ! is_array( $result['added'] ) || ! $volume ) {
			return;
		}

		$is_admin = class_fma_permissions::has_unrestricted_filesystem_access();
		$written  = $this->collect_written_file_stats( $result['added'], $volume );

		foreach ( $written as $file ) {
			if ( empty( $file['name'] ) || empty( $file['hash'] ) ) {
				continue;
			}

			if ( ! $is_admin && ! class_fma_permissions::is_restricted_write_filename_allowed( $file['name'] ) ) {
				$this->remove_volume_file( $file, $volume );
				continue;
			}

			if ( $this->should_sanitize_as_svg( $file, $volume ) ) {
				$this->sanitize_svg_file_content( $file, $volume );
			}
		}

		// Drop removed restricted files from the client-facing "added" list.
		if ( ! $is_admin ) {
			$result['added'] = array_values(
				array_filter(
					$result['added'],
					function ( $file ) {
						if ( empty( $file['name'] ) ) {
							return true;
						}
						if ( ! empty( $file['mime'] ) && 'directory' === $file['mime'] ) {
							return true;
						}
						return class_fma_permissions::is_restricted_write_filename_allowed( $file['name'] );
					}
				)
			);
		}
	}

	/**
	 * BC alias — older references may still call on_upload_event.
	 */
	public function on_upload_event( $cmd, &$result, $args, $elfinder, $volume = null ) {
		return $this->on_files_written_event( $cmd, $result, $args, $elfinder, $volume );
	}

	/**
	 * Whether a file stat represents an SVG by name/MIME.
	 *
	 * @param array $file elFinder file stat.
	 * @return bool
	 */
	private function is_svg_file_stat( $file ) {
		$lower = isset( $file['name'] ) ? strtolower( $file['name'] ) : '';
		$mime  = isset( $file['mime'] ) ? (string) $file['mime'] : '';

		return (bool) preg_match( '/\.svgz?$/', $lower )
			|| ( false !== strpos( $mime, 'svg' ) );
	}

	/**
	 * Decide if SVG sanitiser should run (name/MIME or content sniff).
	 *
	 * @param array       $file         elFinder file stat.
	 * @param object      $volume       Volume driver.
	 * @param string|null $content_hint Optional in-memory content (e.g. put body).
	 * @return bool
	 */
	private function should_sanitize_as_svg( $file, $volume, $content_hint = null ) {
		if ( $this->is_svg_file_stat( $file ) ) {
			return true;
		}

		if ( null !== $content_hint ) {
			return $this->file_content_looks_like_svg( $content_hint );
		}

		$content = $this->read_volume_file_content( $file, $volume );
		return $this->file_content_looks_like_svg( $content );
	}

	/**
	 * Content-based SVG detection from parsed XML (AFM-974 / WPScan).
	 *
	 * Root elements in the SVG namespace are detected regardless of prefix;
	 * when XML cannot be parsed but still looks SVG-like, fail closed.
	 *
	 * @param string $content File contents.
	 * @return bool
	 */
	private function file_content_looks_like_svg( $content ) {
		if ( ! is_string( $content ) || '' === $content ) {
			return false;
		}

		// Only sniff text-ish payloads; skip obvious binaries.
		if ( false !== strpos( substr( $content, 0, 512 ), "\0" ) ) {
			return false;
		}

		$content = ltrim( $content, "\xEF\xBB\xBF \t\n\r\0\x0B" );
		if ( '' === $content ) {
			return false;
		}

		if ( $this->parsed_content_has_svg_root( $content ) ) {
			return true;
		}

		return $this->unparsed_content_may_be_svg( $content );
	}

	/**
	 * Detect SVG root via DOM (namespace-aware, full document).
	 *
	 * @param string $content XML/SVG file contents.
	 * @return bool
	 */
	private function parsed_content_has_svg_root( $content ) {
		if ( ! class_exists( 'DOMDocument' ) ) {
			return false;
		}

		$previous = libxml_use_internal_errors( true );

		$dom = new DOMDocument();
		$loaded = $dom->loadXML(
			$content,
			LIBXML_NONET | LIBXML_COMPACT
		);

		libxml_clear_errors();
		libxml_use_internal_errors( $previous );

		if ( ! $loaded ) {
			return false;
		}

		$root = $dom->documentElement;
		if ( ! $root instanceof DOMElement ) {
			return false;
		}

		return $this->dom_element_is_svg( $root );
	}

	/**
	 * Whether a DOM element is an SVG document root.
	 *
	 * @param DOMElement $element Document root element.
	 * @return bool
	 */
	private function dom_element_is_svg( DOMElement $element ) {
		if ( 'svg' !== strtolower( $element->localName ) ) {
			return false;
		}

		$namespace = $element->namespaceURI;

		return ( null === $namespace || '' === $namespace || 'http://www.w3.org/2000/svg' === $namespace );
	}

	/**
	 * Fail-closed fallback when XML parsing fails but payload may still be SVG.
	 *
	 * @param string $content File contents.
	 * @return bool
	 */
	private function unparsed_content_may_be_svg( $content ) {
		if ( '<' !== $content[0] && 0 !== strpos( $content, '<?xml' ) ) {
			return false;
		}

		return (bool) preg_match( '/<\s*(?:[\w.-]+:)?svg\b/i', $content );
	}

	/**
	 * Read volume file contents for SVG detection (full file, capped).
	 *
	 * @param array  $file      File stat.
	 * @param object $volume    Volume driver.
	 * @param int    $max_bytes Maximum bytes to read.
	 * @return string
	 */
	private function read_volume_file_content( $file, $volume, $max_bytes = 10485760 ) {
		if ( empty( $file['hash'] ) || ! method_exists( $volume, 'getPath' ) ) {
			return '';
		}

		$path = $volume->getPath( $file['hash'] );
		if ( ! $path || ! is_file( $path ) || ! is_readable( $path ) ) {
			return '';
		}

		$size = filesize( $path );
		if ( false === $size || $size < 1 ) {
			return '';
		}

		if ( $size <= $max_bytes ) {
			$content = file_get_contents( $path );
			return is_string( $content ) ? $content : '';
		}

		$fh = fopen( $path, 'rb' );
		if ( ! $fh ) {
			return '';
		}
		$content = fread( $fh, $max_bytes );
		fclose( $fh );

		return is_string( $content ) ? $content : '';
	}

	/**
	 * Flatten added stats so nested extract contents are included.
	 *
	 * @param array $added  elFinder added stats.
	 * @param object $volume Volume driver.
	 * @return array
	 */
	private function collect_written_file_stats( $added, $volume ) {
		$files = array();

		foreach ( $added as $file ) {
			if ( empty( $file['hash'] ) ) {
				continue;
			}
			if ( ! empty( $file['mime'] ) && 'directory' === $file['mime'] ) {
				$files = array_merge( $files, $this->collect_files_under_hash( $file['hash'], $volume ) );
				continue;
			}
			$files[] = $file;
		}

		return $files;
	}

	/**
	 * Recursively list file stats under a directory hash.
	 *
	 * @param string $hash   Directory hash.
	 * @param object $volume Volume driver.
	 * @return array
	 */
	private function collect_files_under_hash( $hash, $volume ) {
		$files = array();

		if ( ! method_exists( $volume, 'scandir' ) ) {
			return $files;
		}

		$items = $volume->scandir( $hash );
		if ( ! is_array( $items ) ) {
			return $files;
		}

		foreach ( $items as $item ) {
			if ( empty( $item['hash'] ) ) {
				continue;
			}
			if ( ! empty( $item['mime'] ) && 'directory' === $item['mime'] ) {
				$files = array_merge( $files, $this->collect_files_under_hash( $item['hash'], $volume ) );
				continue;
			}
			$files[] = $item;
		}

		return $files;
	}

	/**
	 * Delete a volume file from disk (and best-effort via volume API).
	 *
	 * @param array  $file   File stat.
	 * @param object $volume Volume driver.
	 * @return void
	 */
	private function remove_volume_file( $file, $volume ) {
		if ( empty( $file['hash'] ) ) {
			return;
		}

		if ( method_exists( $volume, 'rm' ) ) {
			try {
				$volume->rm( $file['hash'] );
				return;
			} catch ( Exception $e ) {
				// Fall through to unlink.
			}
		}

		if ( method_exists( $volume, 'getPath' ) ) {
			$path = $volume->getPath( $file['hash'] );
			if ( $path && is_file( $path ) ) {
				@unlink( $path );
			}
		}
	}

	private function sanitize_svg_file_content( $uploaded_file, $volume ) {
		require_once FMAFILEPATH . 'application/svg-sanitizer/includes/autoload.php';
		if ( empty( $uploaded_file['hash'] ) || ! method_exists( $volume, 'getPath' ) ) {
			return;
		}

		$file_path = $volume->getPath( $uploaded_file['hash'] );
		if ( ! $file_path || ! file_exists( $file_path ) ) {
			return;
		}

		$file_content  = file_get_contents( $file_path );
		$svg_sanitizer = new enshrined\svgSanitize\Sanitizer();
		$sanitized     = $svg_sanitizer->sanitize( $file_content );

		// Sanitiser returns false on hard failure — never leave the original XSS payload.
		if ( false === $sanitized || null === $sanitized ) {
			$sanitized = '';
		}

		file_put_contents( $file_path, $sanitized );
	}

	/**
	 * Intercept search command to handle content search tag parameter
	 * This hook is called before the search command executes
	 * We decode the tag from q parameter which contains special marker
	 */
	public function on_search_command( $cmd, &$args, $elfinder, $volume ) {
		if ( $cmd === 'search' ) {
			$type = !empty( $args['type'] ) ? $args['type'] : '';
			$q = !empty( $args['q'] ) ? trim( $args['q'] ) : '';

			// Content search: type is SearchTag and/or q contains our marker
			if ( $type === 'SearchTag' || strpos( $q, '__CONTENT_SEARCH__:' ) === 0 ) {
				$tag = '';
				if ( strpos( $q, '__CONTENT_SEARCH__:' ) === 0 ) {
					$tag = substr( $q, strlen( '__CONTENT_SEARCH__:' ) );
				} elseif ( $type === 'SearchTag' && $q !== '' ) {
					$tag = $q; // default elFinder sends type=SearchTag and q=term (no marker)
				}
				if ( $tag !== '' ) {
					elFinderVolumefma_local_filesystem::setContentSearchTag( $tag );
					$args['q'] = '';
				} else {
					elFinderVolumefma_local_filesystem::setContentSearchTag( '' );
				}
			} else {
				elFinderVolumefma_local_filesystem::setContentSearchTag( '' );
			}
		}
	}
}
/**
* Hook to fix invalid and malicious files
*/
function afm_plugin_file_validName( $name ) {

    if ( ! empty( $name ) ) {
		// Check for dangerous characters (but allow spaces)
		// Block characters that could be used for path traversal or command injection
		if( preg_match( '/[<>:"|?*\x00-\x1F]/', $name ) ) {
			return false;
		}
		
		// Block files starting with dot (except for special cases handled elsewhere)
		if( strpos($name, '.') === 0 && $name !== '.' && $name !== '..' ) {
			return false;
		}

        $lower_name = strtolower( $name );

		// Block dangerous file extensions
		if(
			  strpos($lower_name, '.php') !== false
		   || strpos($lower_name, '.phtml') !== false
		   || strpos($lower_name, '.ini') !== false
		   || strpos($lower_name, '.htaccess') !== false
		   || strpos($lower_name, '.config') !== false
		   || strpos($lower_name, '.css') !== false
		   || strpos($lower_name, '.js') !== false
		   || preg_match( '/\.(html?|xhtml|shtml)$/', $lower_name )
		  ) {
			return false;
		}
		
		// Filename is valid (allows spaces)
		return true;
	}
	
	return false;
}
function access($attr, $path, $data, $volume, $isDir, $relpath) {
	$basename = basename($path);
	//skipping htaccess
	if($basename == '.htaccess') {
		return null;
	} else {
	return $basename[0] === '.'                  // if file/folder begins with '.' (dot)
			 && strlen($relpath) !== 1           // but with out volume root
		? !($attr == 'read' || $attr == 'write') // set read+write to false, other (locked+hidden) set to true
		:  null;   // else elFinder decide it itself
	}
	}