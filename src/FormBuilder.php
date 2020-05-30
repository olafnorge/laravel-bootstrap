<?php
namespace olafnorge\Html;

use Collective\Html\FormBuilder as BaseFormBuilder;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Illuminate\Support\Arr;
use Illuminate\Support\HtmlString;
use Illuminate\Support\MessageBag;

class FormBuilder extends BaseFormBuilder {


    /**
     * Open up a new Bootstrap HTML form.
     *
     * @param array $options
     * @return \Illuminate\Support\HtmlString
     */
    public function open(array $options = []) {
        $title = value(function () use ($options): ?string {
            if (Arr::has($options, 'title')) {
                return Arr::get($options, 'title');
            }

            /** @noinspection PhpUndefinedClassInspection */
            if (class_exists(Breadcrumbs::class, true)
                && $this->request->route()
                && Breadcrumbs::exists($this->request->route()->getName())
                && Breadcrumbs::current()
            ) {
                /** @noinspection PhpUndefinedClassInspection */
                return Breadcrumbs::current()->title;
            }

            return null;
        });
        $isHidden = Arr::get($options, 'hidden', false);

        return $this->toHtmlString(implode(array_filter([
            $this->toHtmlString(sprintf('<div class="card %s">', $isHidden ? 'd-none' : '')),
            parent::open(value(function () use ($isHidden, $options) {
                $options['class'] = sprintf('%s %s', Arr::get($options, 'class', ''), $isHidden ? 'd-none' : '');

                return Arr::except($options, ['title', 'hidden']);
            })),
            $title ? $this->toHtmlString(sprintf('<div class="card-header %s">%s</div>', $isHidden ? 'd-none' : '', $title)) : null,
            $this->toHtmlString(sprintf('<div class="card-body %s">', $isHidden ? 'd-none' : '')),
        ])));
    }


    /**
     * Close the current Bootstrap form.
     *
     * @param array $buttons
     * @return string
     */
    public function close(array $buttons = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('</div>'),
            value(function () use ($buttons): ?string {
                $buttons = implode('', array_map(function ($button) {
                    if ($button instanceof HtmlString) {
                        return $button->toHtml();
                    }

                    return $this->toHtmlString($button);
                }, $buttons));

                return $buttons ? $this->toHtmlString(implode('', [
                    $this->toHtmlString('<div class="card-footer clearfix">'),
                    $buttons,
                    $this->toHtmlString('</div>'),
                ])) : null;
            }),
            parent::close(),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function submit($value = null, $options = []) {
        $options['class'] = sprintf('btn btn-primary float-right %s', Arr::get($options, 'class', ''));
        $options['type'] = 'submit';

        return parent::button($this->toHtmlString(implode('', [
            $this->toHtmlString('<i class="fa fa-dot-circle-o"></i>'),
            sprintf(' %s', $value ?: ''),
        ])), $options);
    }


    /**
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function button($value = null, $options = []) {
        $options['class'] = sprintf('btn btn-primary %s', Arr::get($options, 'class', ''));

        return parent::button($value, $options);
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function text($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::text($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function textarea($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::textarea($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param array $options
     * @return HtmlString
     */
    public function password($name, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::password($name, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );
                $options['autocomplete'] = Arr::get($options, 'autocomplete', 'off');

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function email($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::email($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function range($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::range($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function search($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::search($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function tel($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::tel($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function number($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::number($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function date($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::date($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function datetime($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::datetime($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function datetimeLocal($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::datetimeLocal($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function time($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::time($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function url($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::url($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function week($name, $value = null, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::week($name, $value, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param null $value
     * @param array $options
     * @return HtmlString
     */
    public function file($name, $options = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($options, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::file($name, value(function () use ($options, $name): array {
                $options['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($options, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($options, ['help', 'label']);
            })),
            Arr::has($options, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($options, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }


    /**
     * @param string $name
     * @param array $list
     * @param null $selected
     * @param array $selectAttributes
     * @param array $optionsAttributes
     * @param array $optgroupsAttributes
     * @return HtmlString
     */
    public function select($name, $list = [], $selected = null, array $selectAttributes = [], array $optionsAttributes = [], array $optgroupsAttributes = []) {
        return $this->toHtmlString(implode('', array_filter([
            $this->toHtmlString('<div class="form-group row">'),
            $this->label(
                $name,
                Arr::get($selectAttributes, 'label', ucwords(str_replace(['-', '_'], ' ', $name))),
                ['class' => 'col-md-3 col-form-label']
            ),
            $this->toHtmlString('<div class="col-md-9">'),
            parent::select($name, $list, $selected, value(function () use ($selectAttributes, $name): array {
                $selectAttributes['class'] = sprintf(
                    'form-control %s %s',
                    Arr::get($selectAttributes, 'class', ''),
                    $this->session->get('errors', new MessageBag())->has($name) ? 'is-invalid' : ''
                );

                return Arr::except($selectAttributes, ['help', 'label']);
            }), $optionsAttributes, $optgroupsAttributes),
            Arr::has($selectAttributes, 'help')
                ? $this->toHtmlString(sprintf('<div class="help-block text-muted">%s</div>', Arr::get($selectAttributes, 'help')))
                : null,
            $this->session->get('errors', new MessageBag())->has($name)
                ? $this->toHtmlString(sprintf('<div class="invalid-feedback">%s</div>', $this->session->get('errors', new MessageBag())->first($name)))
                : null,
            $this->toHtmlString('</div>'),
            $this->toHtmlString('</div>'),
        ])));
    }
}
