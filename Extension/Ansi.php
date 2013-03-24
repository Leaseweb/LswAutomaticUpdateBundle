<?php
namespace Lsw\AutomaticUpdateBundle\Extension;

class Ansi
{
    public static function ansi2html($str) {

        $css = '';
        $colors = array('black', 'maroon', 'green', 'olive', 'navy', 'purple', 'teal', 'silver', 'gray', 'red', 'lime', 'yellow', 'blue', 'fuchsia', 'aqua', 'white');
        // Default styles.
        $styles = array(
            'background'   => null,  // Default is defined by the stylesheet.
            'blink'        => false,
            'bold'         => false,
            'color'        => null,  // Default is defined by the stylesheet.
//          'inverse'      => false, // Cannot be expressed in terms of CSS!
            'italic'       => false, // Not supported by DarkOwl's ANSI.
            'line-through' => false, // Not supported by DarkOwl's ANSI.
            'underline'    => false,
        );

        $ascii2entities = function ($string) {
            for($i = 128; $i <= 255; $i++) {
                $entity = htmlentities(chr($i), ENT_QUOTES);
                $temp = substr($entity, 0, 1);
                $temp .= substr($entity, -1, 1);
                if ($temp != '&;') {
                    $string = str_replace(chr($i), '', $string);
                } else {
                    $string = str_replace(chr($i), $entity, $string);
                }
            }
            return $string;
        };

        $ansi_decode = function ($matches) use (&$css,&$colors,&$styles) {
            // Copy the previous styles.
            $newstyles = $styles;
            // Extract the codes from the escape sequences.
            preg_match_all('/\d+/', $matches[0], $matches);

            // Walk through the codes.
            foreach ($matches[0] as $code) {
                switch ($code) {
                case '0':
                    // Reset all styles.
                    $newstyles['background']   = null;
                    $newstyles['blink']        = false;
                    $newstyles['bold']         = false;
                    $newstyles['color']        = null;
    //              $newstyles['inverse']      = false;
                    $newstyles['italic']       = false;
                    $newstyles['line-through'] = false;
                    $newstyles['underline']    = false;
                    break;
                case '1':
                    // Set the bold style.
                    $newstyles['bold'] = true;
                    break;
                case '3':
                    // Set the italic style.
                    $newstyles['italic'] = true;
                    break;
                case '4':
                case '21': // Actually double underline, but CSS doesn't support that yet.
                    // Set the underline style.
                    $newstyles['underline'] = true;
                    break;
                case '5':
                case '6': // Actually rapid blinking, but CSS doesn't support that.
                    // Set the blink style.
                    $newstyles['blink'] = true;
                    break;
    //          case '7':
    //              // Set the inverse style.
    //              $newstyles['inverse'] = true;
    //              break;
                case '9':
                    // Set the line-through style.
                    $newstyles['line-through'] = true;
                    break;
                case '2': // Previously incorrectly interpreted by Pueblo/UE as cancel bold, now still supported for backward compatibility.
                case '22':
                    // Reset the bold style.
                    $newstyles['bold'] = false;
                    break;
                case '23':
                    // Reset the italic style.
                    $newstyles['italic'] = false;
                    break;
                case '24':
                    // Reset the underline style.
                    $newstyles['underline'] = false;
                    break;
                case '25':
                    // Reset the blink style.
                    $newstyles['blink'] = false;
                    break;
    //          case '27':
    //              // Reset the inverse style.
    //              $newstyles['inverse'] = false;
    //              break;
                case '29':
                    // Reset the line-through style.
                    $newstyles['line-through'] = false;
                    break;
                case '30': case '31': case '32': case '33': case '34': case '35': case '36': case '37':
                    // Set the foreground color.
                    $newstyles['color'] = $code - 30;
                    break;
                case '39':
                    // Reset the foreground color.
                    $newstyles['color'] = null;
                    break;
                case '40': case '41': case '42': case '43': case '44': case '45': case '46': case '47':
                    // Set the background color.
                    $newstyles['background'] = $code - 40;
                    break;
                case '49':
                    // Reset the background color.
                    $newstyles['background'] = null;
                    break;
                default:
                    // Unsupported code; simply ignore.
                    break;
                }
            }

            if ($newstyles === $styles) {
                // Styles are effectively unchanged; return nothing.
                return '';
            }

            // Copy the new styles.
            $styles = $newstyles;
            // If there's a previous CSS in effect, close the <span>.
            $html = $css ? '</span>' : '';
            // Generate CSS.
            $css = '';

            // background-color property.
            if (!is_null($styles['background'])) {
                $css .= ($css ? '; ' : '') . "background-color: {$colors[$styles['background']]}";
            }

            // text-decoration property.
            if ($styles['blink'] || $styles['line-through'] || $styles['underline']) {
                $css .= ($css ? '; ' : '') . 'text-decoration:';
                if ($styles['blink']) {
                    $css .= ' blink';
                }
                if ($styles['line-through']) {
                    $css .= ' line-through';
                }
                if ($styles['underline']) {
                    $css .= ' underline';
                }
            }

            // font-weight property.
            if ($styles['bold'] && is_null($styles['color'])) {
                $css .= ($css ? '; ' : '') . 'font-weight: bold';
            }

            // color property.
            if (!is_null($styles['color'])) {
                $css .= ($css ? '; ' : '') . "color: {$colors[$styles['color'] | $styles['bold'] << 3]}";
            }

            // font-style property.
            if ($styles['italic']) {
                $css .= ($css ? '; ' : '') . 'font-style: italic';
            }

            // Generate and return the HTML.
            if ($css) {
                $html .= "<span style=\"$css\">";
            }
            return $html;
        };


        // Replace special characters to their corresponding HTML entities
        $str = $ascii2entities($str);
        // Replace ANSI codes.
        $str = preg_replace_callback('/(?:\e\[\d+(?:;\d+)*m)+/', $ansi_decode, "$str\033[0m");
        // Strip ASCII bell.
        $str = str_replace("\007", '', $str);
        // Return the parsed string.
        return $str;
    }
}
