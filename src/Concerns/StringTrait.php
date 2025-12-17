<?php

namespace Nacosvel\Helper\Concerns;

trait StringTrait
{
    /**
     * Convert a camelCase or PascalCase string to kebab-case.
     *
     * 将 小驼峰命名法（camelCase）或大驼峰命名（PascalCase）的字符串转换为 kebab-case。
     * (?<!^): 这个负向前瞻断言确保匹配的字母不是字符串的第一个字符。
     * [A-Z](?=[a-z]): 这是一个断言，它只会匹配后面跟随小写字母的大写字母，防止处理像 HTTPResponse 这样连续大写字母的情况。
     * (?<=[a-z])[A-Z]: 断言前面是小写字母的大写字母，适用于处理正常的 camelCase。
     *
     * This function transforms a string formatted in camelCase (e.g., "myExampleString")
     * or PascalCase (e.g., "MyExampleString") into kebab-case (e.g., "my-example-string").
     * Optionally, a callback can be applied to the input string before conversion.
     *
     * @param string        $input    The camelCase or PascalCase input string.
     * @param callable|null $callback Optional callback applied to the input string before conversion.
     *                                Signature: fn(string $input): string
     *
     * @return string The converted string in kebab-case format.
     *
     * @example
     * ```
     * $result = camelToKebab('myExampleString');
     * // $result = 'my-example-string'
     *
     * $result = camelToKebab('MyExampleString', fn($s) => strtoupper($s));
     * // $result = 'MY-EXAMPLE-STRING'
     * ```
     */
    public static function camelToKebab(string $input, callable $callback = null): string
    {
        // Convert camelCase or PascalCase to kebab-case, handling consecutive uppercase letters
        $output = preg_replace('/(?<!^)([A-Z](?=[a-z])|(?<=[a-z])[A-Z])/', '-$1', $input);

        // Convert to lowercase and return
        $output = strtolower($output);

        return is_callable($callback) ? call_user_func($callback, $output) : $output;
    }

    /**
     * Convert a kebab-case string to camelCase.
     *
     * This function transforms a string formatted in kebab-case (e.g., "my-example-string")
     * into camelCase (e.g., "myExampleString"). Optionally, a callback can be provided
     * to further process or modify the resulting camelCase string.
     *
     * @param string        $input    The kebab-case input string.
     * @param callable|null $callback Optional callback applied to the resulting camelCase string.
     *                                Signature: fn(string $camel): string
     *
     * @return string The converted string in camelCase format, optionally processed by the callback.
     *
     * @example
     * ```
     * $result = kebabToCamel('my-example-string');
     * // $result = 'myExampleString'
     *
     * $result = kebabToCamel('my-example-string', fn($s) => strtoupper($s));
     * // $result = 'MYEXAMPLESTRING'
     * ```
     */
    public static function kebabToCamel(string $input, callable $callback = null): string
    {
        // Use preg_replace_callback to find '-' followed by a lowercase letter
        $output = preg_replace_callback('/-(\w)/', function ($matches) {
            return strtoupper($matches[1]); // Convert the letter after '-' to uppercase
        }, strtolower($input));

        // Return the camelCase version
        return is_callable($callback) ? call_user_func($callback, lcfirst($output)) : lcfirst($output); // Ensure the first character is lowercase
    }

    /**
     * Convert a kebab-case string to PascalCase.
     *
     * This function transforms a string formatted in kebab-case (e.g., "my-example-string")
     * into PascalCase (e.g., "MyExampleString"). Optionally, a callback can be provided
     * to further process or modify the resulting PascalCase string.
     *
     * @param string        $input    The kebab-case input string.
     * @param callable|null $callback Optional callback applied to the resulting PascalCase string.
     *                                Signature: fn(string $pascal): string
     *
     * @return string The converted string in PascalCase format, optionally processed by the callback.
     *
     * @example
     * ```
     * $result = kebabToPascal('my-example-string');
     * // $result = 'MyExampleString'
     *
     * $result = kebabToPascal('my-example-string', fn($s) => strtolower($s));
     * // $result = 'myexamplestring'
     * ```
     */
    public static function kebabToPascal(string $input, callable $callback = null): string
    {
        // Use preg_replace_callback to find '-' followed by a lowercase letter
        $output = preg_replace_callback('/-(\w)/', function ($matches) {
            return strtoupper($matches[1]); // Convert the letter after '-' to uppercase
        }, strtolower($input));

        // Capitalize the first character to match PascalCase
        return is_callable($callback) ? call_user_func($callback, ucfirst($output)) : ucfirst($output); // Ensure the first character is uppercase
    }
}
