<?php

/* modules/webform/templates/webform-progress-tracker.html.twig */
class __TwigTemplate_4e8467def3bef997c323386cbd7714d11cc3e7fd05c26a2069ffa1ce55b1f382 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $tags = array("for" => 22, "set" => 24, "if" => 32);
        $filters = array("length" => 32);
        $functions = array("attach_library" => 19);

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                array('for', 'set', 'if'),
                array('length'),
                array('attach_library')
            );
        } catch (Twig_Sandbox_SecurityError $e) {
            $e->setSourceContext($this->getSourceContext());

            if ($e instanceof Twig_Sandbox_SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof Twig_Sandbox_SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

        // line 19
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->env->getExtension('Drupal\Core\Template\TwigExtension')->attachLibrary("webform/webform.progress.tracker"), "html", null, true));
        echo "

<ul class=\"webform-progress-tracker progress-tracker progress-tracker--center\">
  ";
        // line 22
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["progress"] ?? null));
        foreach ($context['_seq'] as $context["index"] => $context["page"]) {
            // line 23
            echo "  ";
            // line 24
            $context["classes"] = array(0 => "progress-step", 1 => (((            // line 26
$context["index"] < ($context["current_index"] ?? null))) ? ("is-complete") : ("")), 2 => (((            // line 27
$context["index"] == ($context["current_index"] ?? null))) ? ("is-active") : ("")));
            // line 30
            echo "  <li";
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["attributes"] ?? null), "setAttribute", array(0 => "data-webform-page", 1 => $this->getAttribute($context["page"], "name", array())), "method"), "setAttribute", array(0 => "class", 1 => ""), "method"), "addClass", array(0 => ($context["classes"] ?? null)), "method"), "html", null, true));
            echo ">
    <span class=\"progress-marker\">";
            // line 31
            echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["index"] + 1), "html", null, true));
            echo "</span>
    ";
            // line 32
            if ((twig_length_filter($this->env, ($context["progress"] ?? null)) < ($context["max_pages"] ?? null))) {
                // line 33
                echo "      <span class=\"progress-text\">
        <div class=\"progress-title\">";
                // line 34
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->getAttribute($context["page"], "title", array()), "html", null, true));
                echo "</div>
      </span>
    ";
            }
            // line 37
            echo "  </li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['index'], $context['page'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 39
        echo "</ul>
";
    }

    public function getTemplateName()
    {
        return "modules/webform/templates/webform-progress-tracker.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  86 => 39,  79 => 37,  73 => 34,  70 => 33,  68 => 32,  64 => 31,  59 => 30,  57 => 27,  56 => 26,  55 => 24,  53 => 23,  49 => 22,  43 => 19,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "modules/webform/templates/webform-progress-tracker.html.twig", "/Applications/MAMP/htdocs/Test/modules/webform/templates/webform-progress-tracker.html.twig");
    }
}
