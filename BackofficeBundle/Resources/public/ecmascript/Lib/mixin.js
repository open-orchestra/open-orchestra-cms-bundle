let mix = (superclass) => new MixinBuilder(superclass);
/**
 * Class to apply a list of mixins
 * Usage :
 *      class X extends mix(Object).with(A, B, C) {}
 *
 */
class MixinBuilder {
    constructor(superclass) {
        this.superclass = superclass;
    }

    with(...mixins) {
        return mixins.reduce((c, mixin) => mixin(c), this.superclass);
    }
}
