<?php

namespace Nacosvel\Helper\Concerns;

trait StringTrait
{
    /**
     * 将 小驼峰命名法（camelCase）或大驼峰命名（PascalCase）的字符串转换为 kebab-case。
     * (?<!^): 这个负向前瞻断言确保匹配的字母不是字符串的第一个字符。
     * [A-Z](?=[a-z]): 这是一个断言，它只会匹配后面跟随小写字母的大写字母，防止处理像 HTTPResponse 这样连续大写字母的情况。
     * (?<=[a-z])[A-Z]: 断言前面是小写字母的大写字母，适用于处理正常的 camelCase。
     *
     * @param string        $input
     * @param callable|null $callback
     *
     * @return string
     */
    public static function camelToKebab(string $input, callable $callback = null): string
    {
        if (is_callable($callback)) {
            $input = call_user_func($callback, $input);
        }

        // Convert camelCase or PascalCase to kebab-case, handling consecutive uppercase letters
        $output = preg_replace('/(?<!^)([A-Z](?=[a-z])|(?<=[a-z])[A-Z])/', '-$1', $input);

        // Convert to lowercase and return
        return strtolower($output);
    }

    /**
     * 将 kebab-case 的字符串转换为小驼峰命名法（camelCase）。
     * 该函数需要识别连字符 - 并将它后面的字母转换为大写。
     *
     * @param string        $input
     * @param callable|null $callback
     *
     * @return string
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
     * 将 kebab-case 的字符串转换为大驼峰命名法（PascalCase）。
     *
     * @param string        $input
     * @param callable|null $callback
     *
     * @return string
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
