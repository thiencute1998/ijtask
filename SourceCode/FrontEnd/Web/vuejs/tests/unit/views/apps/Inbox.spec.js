import {shallowMount, mount, createLocalVue} from '@vue/test-utils';
import VueRouter from 'vue-router'
import BootstrapVue from 'bootstrap-vue'
import Inbox from '@/views/apps/email/Inbox'

const localVue = createLocalVue()
localVue.use(VueRouter)
const router = new VueRouter()

localVue.use(BootstrapVue)

describe('Inbox.vue', () => {
  it('has a name', () => {
    expect(Inbox.name).toMatch('inbox')
  })
  it('is Vue instance', () => {
    const wrapper = shallowMount(Inbox, {
      localVue,
      router
    })
    expect(wrapper.isVueInstance()).toBe(true)
  })
  it('is Inbox', () => {
    const wrapper = shallowMount(Inbox, {
      localVue,
      router
    })
    expect(wrapper.is(Inbox)).toBe(true)
  })
  it('should render correct content', () => {
    const wrapper = mount(Inbox, {
      localVue,
      router
    })
    expect(wrapper.find('div.email-app > nav > .btn').text()).toMatch('New Email')
  })
  test('renders correctly', () => {
    const wrapper = shallowMount(Inbox, {
      localVue,
      router
    })
    expect(wrapper.element).toMatchSnapshot()
  })
})
