<?php

/* modules/webform/templates/webform-progress.html.twig */
class __TwigTemplate_81b51cb99646b2f183846ef3171a229f78a8635e330186dcb568a2d219da5aa9 extends Twig_Template
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
        $tags = array("if" => 26);
        $filters = array();
        $functions = array("attach_library" => 20);

        try {
            $this->env->getExtension('Twig_Extension_Sandbox')->checkSecurity(
                array('if'),
                array(),
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

        // line 20
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, $this->env->getExtension('Drupal\Core\Template\TwigExtension')->attachLibrary("webform/webform.progress"), "html", null, true));
        echo "

<div class=\"webform-progress\">

  ";
        // line 24
        echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["bar"] ?? null), "html", null, true));
        echo "

  ";
        // line 26
        if ((($context["summary"] ?? null) || ($context["percentage"] ?? null))) {
            // line 27
            echo "    <div class=\"webform-progress__status\">
      ";
            // line 28
            if (($context["summary"] ?? null)) {
                // line 29
                echo "        <span class=\"webform-progress__summary\">";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["summary"] ?? null), "html", null, true));
                echo "</span>
        ";
                // line 30
                if (($context["percentage"] ?? null)) {
                    // line 31
                    echo "          <span class=\"webform-progress__percentage\">(";
                    echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["percentage"] ?? null), "html", null, true));
                    echo ")</span>
        ";
                }
                // line 33
                echo "      ";
            } else {
                // line 34
                echo "        <span class=\"webform-progress__percentage\">";
                echo $this->env->getExtension('Twig_Extension_Sandbox')->ensureToStringAllowed($this->env->getExtension('Drupal\Core\Template\TwigExtension')->escapeFilter($this->env, ($context["percentage"] ?? null), "html", null, true));
                echo "</span>
      ";
            }
            // line 36
            echo "    </div>
  ";
        }
        // line 38
        echo "
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/webform/templates/webform-progress.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  88 => 38,  84 => 36,  78 => 34,  75 => 33,  69 => 31,  67 => 30,  62 => 29,  60 => 28,  57 => 27,  55 => 26,  50 => 24,  43 => 20,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "modules/webform/templates/webform-progress.html.twig", "/Applications/MAMP/htdocs/Test/modules/webform/templates/webform-progress.html.twig");
    }
}
