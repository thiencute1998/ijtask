import {shallowMount, mount, createLocalVue} from '@vue/test-utils';
import VueRouter from 'vue-router'
import BootstrapVue from 'bootstrap-vue'
import Compose from '@/views/apps/email/Compose'

const localVue = createLocalVue()
localVue.use(VueRouter)
const router = new VueRouter()

localVue.use(BootstrapVue)

describe('Compose.vue', () => {
  it('has a name', () => {
    expect(Compose.name).toMatch('compose')
  })
  it('is Vue instance', () => {
    const wrapper = shallowMount(Compose, {
      localVue,
      router
    })
    expect(wrapper.isVueInstance()).toBe(true)
  })
  it('is Compose', () => {
    const wrapper = shallowMount(Compose, {
      localVue,
      router
    })
    expect(wrapper.is(Compose)).toBe(true)
  })
  it('should render correct content', () => {
    const wrapper = mount(Compose, {
      localVue,
      router
    })
    expect(wrapper.find('main > p.text-center').text()).toMatch('New Message')
  })
  it('should render correct content', () => {
    const wrapper = mount(Compose, {
      localVue,
      router
    })
    expect(wrapper.find('div.email-app > nav > .btn').text()).toMatch('New Email')
  })
  test('renders correctly', () => {
    const wrapper = shallowMount(Compose, {
      localVue,
      router
    })
    expect(wrapper.element).toMatchSnapshot()
  })
})

